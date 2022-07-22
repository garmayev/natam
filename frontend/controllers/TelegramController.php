<?php

namespace frontend\controllers;

use common\models\Client;
use common\models\Order;
use common\models\OrderProduct;
use common\models\TelegramMessage;
use frontend\commands\Command;
use frontend\models\Staff;
use frontend\models\Telegram;
use frontend\models\Updates;
use common\models\User;
use garmayev\staff\models\Employee;
use yii\hepers\Url;
use Yii;

/**
 *
 */
class TelegramController extends \yii\rest\Controller
{
	protected function findUser($chat_id) {
		$employee = Employee::find()->where(["chat_id" => $chat_id])->one();
		if ( $employee ) {
			$user = $employee->user;
		} else {
			$client = Client::findOne(['chat_id' => $chat_id]);
			if ($client) {
				$user = Client::findOne(["chat_id" => $chat_id])->user;
			} else {
				return null;
			}
//			$user = Client::findOne(["chat_id" => $chat_id])->user;
		}
		return $user;
	}

	protected static function checkPermission($telegram, $permission) {
		if ( !Yii::$app->user->can($permission) ) {
//			$message = isset($telegram->input->message) ? $telegram->input->message : $telegram->input->callback_query;
//			$result = $telegram->sendMessage([
//				'chat_id' => $message->chat->id,
//				"text" => Yii::t("telegram", "You don`t have permissions for this action")
//			]);
			return false;
		}
		return true;
	}

	public function beforeAction($action)
	{
		$data = json_decode(file_get_contents("php://input"), true);
		// Yii::error($data);
		if ( isset($data["callback_query"]) ) {
			$user = $this->findUser($data["callback_query"]["from"]["id"]);
		} elseif( isset($data["message"]) ) {
			if (preg_match('/(\/start)/', $data['message']['text'])) {
				return parent::beforeAction($action);
			} else {
				$user = $this->findUser($data["message"]["from"]["id"]);
			}
		} else {
			return false;
		}
		if ( empty($user) ) {
			$this->asJson([
				'code' => 'ERROR',
				'message' => 'You are not logged',
			]);
			Yii::error([
				"input_data" => $data,
				"message" => 'user is not logged',
			]);
			return false;
		}
		Yii::$app->user->switchIdentity($user, 0);
		$this->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}

	public function actionCallback()
	{
		$input = json_decode(file_get_contents("php://input"), true);
		$telegram = new Telegram($input);
		if ( isset($telegram->message) ) {
			$text = $telegram->message["text"];
			$args = explode(" ", $text);
			$command = array_shift($args);
			switch ($command) {
				case "/start":
					if ( count($args) > 0 ) {
						$client = Client::findOne(["phone" => $args[0]]);
					} else {
						$client = Client::findOne(["chat_id" => $telegram->message["from"]["id"]]);
					}
					if ( isset($client) ) {
						$client->chat_id = $telegram->message["from"]["id"];
						$client->save();

						$text = "Список ваших заказов";
						$text .= (count($client->orders) === 0) ? " пуст" : ":";
						$markup = ["inline_keyboard" => []];
						foreach (Order::find()->where(["client_id" => $client->id])->andWhere(["<", "status", Order::STATUS_COMPLETE])->all() as $order) {
							$markup["inline_keyboard"][] = [["text" => "Заказ #$order->id", "callback_data" => "/order $order->id"]];
						}
						if ( Telegram::sendMessage(["chat_id" => $client->chat_id, "text" => $text, "reply_markup" => json_encode($markup)]) ) {
							return ["ok" => true];
						}
					} else {
						if (isset($args[0])) {
							$staff = Employee::find()->where(["phone" => $args[0]])->orWhere(["chat_id" => $telegram->message["from"]["id"]])->one();
							if ($staff) {
								if (is_null($staff->chat_id)) {
									$staff->chat_id = $telegram->message["from"]["id"];
									$staff->save();
								}
								Telegram::sendMessage([
									"chat_id" => $staff->chat_id,
									"text" => "В этот бот вам будут приходить новые заявки с сайта " . Yii::$app->name,
								]);
							}
						}
					}
					break;
				case "/all_orders":
					$client = Client::findOne(['user_id' => Yii::$app->user->id]);
					$orders = Order::find()->where(['client_id' => $client->id])->andWhere(["<", "status", Order::STATUS_COMPLETE])->all();
					$telegram->send();
					break;
			}
		} else if ( isset($telegram->callback_query) ) {
			$text = $telegram->callback_query["data"];
			$args = explode(" ", $text);
			$command = array_shift($args);
			switch ($command)
			{
				case "/start":
					$client = Client::find()->where(["phone" => $args[0]])->orWhere(["chat_id" => $telegram->callback_query["from"]["id"]])->one();
					if ( isset($client) ) {
						$client->chat_id = $telegram->callback_query["from"]["id"];
						$client->save();

						$text = "Список ваших заказов";
						$text .= (count($client->orders) === 0) ? " пуст" : ":";
						$markup = ["inline_keyboard" => []];
						foreach (Order::find()->where(["client_id" => $client->id])->andWhere(["<", "status", Order::STATUS_COMPLETE])->all() as $order) {
							$markup["inline_keyboard"][] = [["text" => "Заказ #$order->id", "callback_data" => "/order $order->id"]];
						}
						if ( Telegram::editMessage(["chat_id" => $client->chat_id, "text" => $text, "reply_markup" => json_encode($markup), "message_id" => $telegram->callback_query["message"]["message_id"]]) ) {
							return ["ok" => true];
						}
					}
					break;
				case "/order":
					$order = Order::findOne($args[0]);
					$client = Client::find()->where(["chat_id" => $telegram->callback_query["from"]["id"]])->one();
					$text = "Заказ #$order->id\nАдрес доставки: $order->address\nДата создания заказа: ".\Yii::$app->formatter->asDate($order->created_at, "php: d M Y H:i")."\nСодержимое заказа:\n";
					foreach ($order->products as $product) {
						$text .= "\t$product->title\n\t\t$product->value\n\t\t$product->price\n";
					}
					$text .= "\nОбщая стоимость заказа: $order->price";
					$markup = ["inline_keyboard" => [
						[
							[
								"text" => "Повторить этот заказ",
								"callback_data" => "/reply $order->id"
							]
						],
						[
							["text" => \Yii::t("app", "Cancel"), "callback_data" => "/start $client->phone"]
						]
					]];
					if ( Telegram::editMessage(["chat_id" => $client->chat_id, "text" => $text, "reply_markup" => json_encode($markup), "message_id" => $telegram->callback_query["message"]["message_id"]]) ) {
						return ["ok" => true];
					}
					break;
				case "/reply":
					$order = Order::findOne($args[0]);
					$copy = $order->deepClone();
					$client = $copy->client;
					$copy->created_at = time();
					$copy->status = Order::STATUS_NEW;
					$copy->save();
					break;
				case "/order_complete":
					parse_str($args[0], $argument);
					$order = Order::findOne($argument["id"]);
					$staff = Employee::find()->where(["chat_id" => $telegram->callback_query["from"]["id"]])->one();
					$order->status = $staff->state_id + 1;
					$order->save();
					break;
				case "/order_driver":
					parse_str($args[0], $argument);
					$order = Order::findOne($argument["order_id"]);

					if (isset($argument["driver_id"])) {
						$driver = Employee::findOne($argument["driver_id"]);

						Yii::error($driver->attributes);

						$order->status = Order::STATUS_DELIVERY;
						$order->save();

						TelegramMessage::send($driver, $order);;
					} else {
						$order->status = Order::STATUS_COMPLETE;
						$order->save();
					}

					break;
				case "/order_hold":
					parse_str($args[0], $argument);
					$order = Order::findOne($argument["id"]);
					if ( isset($order) ) {
						Telegram::editMessage([
							"message_id" => $telegram->callback_query["message"]["message_id"],
							"chat_id" => $telegram->callback_query["from"]["id"],
							"text" => "Выберите время для задержки",
							"reply_markup" => json_encode([
								"inline_keyboard" => [
									[
										[
											"text" => "Отложить на 1 час",
											"callback_data" => "/order_hold_by_time id={$order->id}&sec=3600",
										]
									], [
										[
											"text" => "Отложить на 3 час",
											"callback_data" => "/order_hold_by_time id={$order->id}&sec=10800",
										]
									], [
										[
											"text" => "Отложить на 6 час",
											"callback_data" => "/order_hold_by_time id={$order->id}&sec=21600",
										]
									], [
										[
											"text" => "Отложить на сутки",
											"callback_data" => "/order_hold_by_time id={$order->id}&sec=86400",
										]
									],
								]
							]),
						]);
					}
					break;
				case "/order_hold_by_time":
					parse_str($args[0], $argument);
					$order = Order::findOne($argument["id"]);
					$seconds = $argument["sec"];
					if ( isset($order) ) {
						$order->hold($seconds);
						$updates = Updates::find()->where(["order_id" => $order->id])->andWhere(["order_status" => $order->status])->all();
						switch ($seconds) {
							case 3600:
								$text = "Заказ #$order->id отложен на 1 час\n";
								break;
							case 10800:
								$text = "Заказ #$order->id отложен на 3 часа\n";
								break;
							case 21600:
								$text = "Заказ #$order->id отложен на 6 часов\n";
								break;
							case 86400:
								$text = "Заказ #$order->id отложен на сутки\n";
								break;
						}
						foreach ($updates as $update) {
							Telegram::editMessage([
								"message_id" => $update->message_id,
								"text" => "{$text}Адрес доставки: $order->address\nДата создания заказа: ".\Yii::$app->formatter->asDate($order->created_at, "php: d M Y H:i")."\nСодержимое заказа:\n",
								"chat_id" => $update->employee->chat_id,
								"reply_markup" => json_encode(["inline_keyboard" => [
									[["text" => "Кладовщику", "callback_data" => "/order_restore id={$order->id}"]]
								]]),
							]);
						}
					}
					break;
				case "/order_restore":
					parse_str($args[0], $argument);
					$order = Order::findOne($argument["id"]);
					$staff = Employee::find()->where(["chat_id" => $telegram->callback_query["from"]["id"]])->one();
					if ( isset($staff) && isset($order) && ($staff->state_id == $order->status) ) {
						$updates = Updates::find()->where(["order_id" => $order->id])->andWhere(["order_status" => $order->status])->all();
						foreach ($updates as $update) {
							Telegram::editMessage(["chat_id" => $update->employee->chat_id, "message_id" => $update->message_id, "text" => "Статус заказа #{$order->id} был изменен"]);
							// $update->delete();
						}
						$order->status = Order::STATUS_PREPARE;
						$order->save();
					}
					break;
			}
		}
		return ["ok" => false];
	}

	public function actionCheck()
	{
		$telegram = Yii::$app->telegram;
		Command::run("/start", [$this, "start"]);
		Command::run("/manager", [$this, "manager"]);
		Command::run("/store", [$this, "store"]);
		Command::run("/driver", [$this, "driver"]);

		Command::run("/all_orders", [$this, "orders"]);
		Command::run("/order", [$this, "view"]);
		Command::run("/copy", [$this, "copy"]);
//		Command::run("/status_store", [$this, "status_store"]);
	}

	public static function start($telegram, $args = null)
	{
//		\Yii::error($args);
		if ( isset($args) ) {
			if ( isset($telegram->input->message) ) {
				if (\Yii::$app->user->isGuest) {
					$client = Client::findOne(["phone" => array_keys($args)[0]]);
					$client->chat_id = $telegram->input->message->chat->id;
					$client->save();
				}
				$telegram->sendMessage([
					'chat_id' => $telegram->input->message->chat->id,
					"text" => "Welcome!",
					"reply_markup" => json_encode([
						"keyboard" => [
							[
								["text" => "/all_orders"]
	//						], [
//									["text" => "/new_order"]
							]
						],
						"resize_keyboard" => true,
					])
				]);
			}
		}
	}

	public static function orders($telegram, $args = null)
	{
		if ( self::checkPermission($telegram, "employee") ) {
			$orders = Order::find()->where(["<", "status", Order::STATUS_COMPLETE])->all();
		} else {
			$client = Client::findOne(["user_id" => Yii::$app->user->id]);
			$orders = Order::find()->where(["client_id" => $client->id])->all();
		}

		if ( count($orders) ) {
			$keyboard = [];
			foreach ($orders as $order) {
				$keyboard[] = [
					["text" => "Заказ #{$order->id} Стоимость: {$order->totalPrice}", "callback_data" => "/order order_id={$order->id}"]
				];
			}
			// \Yii::error($keyboard);
			$chat_id = isset($telegram->input->message) ? $telegram->input->message->chat->id : $telegram->input->callback_query->from["id"];
			if ( isset($telegram->input->message) ) {
				$telegram->sendMessage([
					'chat_id' => $chat_id,
					"text" => "Список заказов",
					"reply_markup" => json_encode([
						"inline_keyboard" => $keyboard
					]),
				]);

			} else {
				$telegram->editMessageText([
					"chat_id" => $chat_id,
					"message_id" => $telegram->input->callback_query->message['message_id'],
					"text" => "Список заказов",
					"reply_markup" => json_encode([
						"inline_keyboard" => $keyboard
					])
				]);
			}
		} else {
			$telegram->sendMessage([
				'chat_id' => $telegram->input->message->chat->id,
				"text" => "Заказов нет!\nСперва создайте заказ на сайте https://natam03.ru/",
			]);
		}
	}

	public static function view($telegram, $args = null)
	{
		if (isset($args["order_id"])) {
			$order = Order::findOne($args["order_id"]);
			\Yii::error($order->generateTelegramText());
			$telegram->editMessageText([
				"message_id" => $telegram->input->callback_query->message["message_id"],
				'chat_id' => $telegram->input->callback_query->message["chat"]["id"],
				"text" => $order->generateTelegramText(),
				"parse_mode" => "html",
				"reply_markup" => json_encode([
					"inline_keyboard" => [
						[
							["text" => "Повторить заказ", "callback_data" => "/copy order_id={$order->id}"],
						], [
							["text" => "Перейти на сайт", "url" => "https://".Yii::$app->params['hostName']."/"],
						], [
							["text" => "Назад", "callback_data" => "/all_orders"],
						]
					]
				]),
			]);
		}
	}

	public static function copy($telegram, $args = null)
	{
		\Yii::error($args);
		if (isset($args["order_id"])) {
			$order = Order::findOne($args["order_id"]);
			$copy = $order->deepClone();
			$telegram->editMessageText([
				"message_id" => $telegram->input->callback_query->message["message_id"],
				'chat_id' => $telegram->input->callback_query->message["chat"]["id"],
				"text" => "Ваш заказ успешно повторен",
				"parse_mode" => "html",
				"reply_markup" => json_encode([
					"inline_keyboard" => [
						[
							["text" => "Назад", "callback_data" => "/all_orders"],
						]
					]
				]),
			]);
		}
	}

	public static function inlineCheck($telegram, $args = null)
	{
		if ( self::checkPermission($telegram, "employee") ) {
			if ( isset($args) ) {
				$text = "Check complete!\n\n".json_encode($args);
			} else {
				$text = "Check complete!";
			}
			$result = $telegram->editMessageText([
				'message_id' => $telegram->input->callback_query->message["message_id"],
				'chat_id' => $telegram->input->callback_query->message["chat"]["id"],
				"text" => $text,
			]);
		}
	}

	public static function manager($telegram, $args = null)
	{
		/**
		 * $args = [ "id" => order_id ]
		 */
		if ( self::checkPermission($telegram, "employee") ) {
			if ( isset($args) ) {
				$order = Order::findOne($args["id"]);
				$order->status = Order::STATUS_PREPARE;
				$order->boss_chat_id = null;
				if ( !$order->save() ) {
					Yii::error($order->getErrorSummary(true));
				}
			}
		} else {
			$text = Yii::t("telegram", "You don`t have permissions for this action");
			Yii::error($text);
		}
	}

	public static function store($telegram, $args = null)
	{
		/**
		 * $args = [ "id" => order_id ]
		 */
		if ( self::checkPermission($telegram, "employee") ) {
			if ( isset($args) ) {
				$order = Order::findOne($args["id"]);

				if ( isset($order->delivery_type) && $order->delivery_type === Order::DELIVERY_COMPANY ) {
					$employee = Employee::findOne($args["driver_id"]);
					$order->status = Order::STATUS_DELIVERY;
					$order->boss_chat_id = null;
					if (!$order->save()) {
						Yii::error($order->getErrorSummary(true));
					} else {
						// \Yii::error((isset($employee)) ? $employee->attributes : $employee);
						// \Yii::error($order->attributes);
						TelegramMessage::send($employee, $order);
					}
				} else {
					$order->status = Order::STATUS_COMPLETE;
					\Yii::error( $order->save() );
				}
			}
		} else {
			$text = Yii::t("telegram", "You don`t have permissions for this action");
			Yii::error($text);
		}
	}

	public static function driver($telegram, $args = null)
	{
		/**
		 * $args = [ "id" => order_id ]
		 */
		if ( self::checkPermission($telegram, "employee") ) {
			if ( isset($args) ) {
				$order = Order::findOne($args["id"]);
				$messages = TelegramMessage::find()
					->where(['order_id' => $order->id])
					->andWhere(['status' => TelegramMessage::STATUS_OPENED])
					->all();

				foreach ($messages as $message) {
					$message->hide();
				}
				$order->status = Order::STATUS_COMPLETE;
				$order->boss_chat_id = null;
				if ( !$order->save() ) {
					Yii::error($order->getErrorSummary(true));
				}
			}
		} else {
			$text = Yii::t("telegram", "You don`t have permissions for this action");
			Yii::error($text);
		}
	}
}
