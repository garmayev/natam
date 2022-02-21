<?php

namespace frontend\controllers;

use common\models\Client;
use common\models\Order;
use common\models\OrderProduct;
use frontend\commands\Command;
use frontend\models\Staff;
use frontend\models\Telegram;
use frontend\models\Updates;
use common\models\User;
use garmayev\staff\models\Employee;
use Yii;

/**
 *
 */
class TelegramController extends \yii\rest\Controller
{
	protected static function findUser($chat_id) {
		$employee = Employee::find()->where(["chat_id" => $chat_id])->one();
		if ( $employee ) {
			$user = $employee->user;
		} else {
			$user = Client::findOne(["chat_id" => $chat_id]);
		}
		return $user;
	}

	protected static function checkPermission($telegram, $permission) {
		if ( !Yii::$app->user->can($permission) ) {
			$result = $telegram->sendMessage([
				'chat_id' => $telegram->input->message->chat->id,
				"text" => Yii::t("app", "Sorry, You don`t have permission to this command!")
			]);
			return false;
		}
		return true;
	}

	public function beforeAction($action)
	{
		$data = json_decode(file_get_contents("php://input"), true);
		if ( isset($data["callback_query"]) ) {
			$user = $this->findUser($data["callback_query"]["from"]["id"]);
		} else {
			$user = $this->findUser($data["message"]["from"]["id"]);
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
					if ( isset($staff) && isset($order) && ($staff->state_id == $order->status) ) {
						$updates = Updates::find()->where(["order_id" => $order->id])->andWhere(["order_status" => $order->status])->all();
						foreach ($updates as $update) {
							// Yii::error($update->attributes);
							$response = Telegram::editMessage(["chat_id" => $update->employee->chat_id, "message_id" => $update->message_id, "text" => "Статус заказа #{$order->id} был изменен"]);
							// $update->delete();
						}
						$order->status++;
						$order->save();
					}
					break;
				case "/order_driver":
					parse_str($args[0], $argument);
					$order = Order::findOne($argument["order_id"]);
					$driver = Employee::findOne($argument["driver_id"]);
					$updates = Updates::find()->where(["order_id" => $order->id])->andWhere(["order_status" => $order->status])->all();
					foreach ($updates as $update) {
//						Telegram::editMessage([
//							"chat_id" => $update->employee->chat_id,
//							"text" => "Статус заказа #{$order->id} был изменен",
//							"message_id" => $update->message_id
//						]);
						// $update->delete();
					}

					$order->status = Order::STATUS_DELIVERY;
					$order->save();

					Telegram::sendMessage([
						"chat_id" => $driver->chat_id,
						"text" => $order->generateTelegramText(),
						"parse_mode" => "HTML",
						"reply_markup" => json_encode(["inline_keyboard" => [$order->generateTelegramKeyboard()]])
					]);
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
//						$updates = Updates::find()->where(["order_id" => $order->id])->andWhere(["order_status" => $order->status])->all();
//						foreach ($updates as $update) {
//							Telegram::editMessage([
//								"message_id" => $update->message_id,
//								"text" => "Заказ #$order->id отложен\nАдрес доставки: $order->address\nДата создания заказа: ".\Yii::$app->formatter->asDate($order->created_at, "php: d M Y H:i")."\nСодержимое заказа:\n",
//								"chat_id" => $update->employee->chat_id,
//								"reply_markup" => json_encode(["inline_keyboard" => [
//									[["text" => "Кладовщику", "callback_data" => "/order_restore id={$order->id}"]]
//								]]),
//							]);
//						}
//						$order->status = Order::STATUS_HOLD;
//						$order->save();
//						return ["ok" => true];
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
		Command::run("/check", function ($telegram, $args) {
			if ( $this->checkPermission($telegram, "employee") ) {
				$result = $telegram->sendMessage([
					'chat_id' => $telegram->input->message->chat->id,
					"text" => "Check complete\nCommands: ".json_encode($args)
				]);
			}
		});
		switch ($telegram->input->callback_query->data) {
			case "/check":
				$callback = $telegram->input->callback_query;
				Yii::$app->telegram->answerCallbackQuery([
					"callback_query_id" => $callback->id,
					"text" => "Something wrong?",
				]);
				break;
		}
//		if ( isset($data["callback_query"]) ) {
//			$chat_id = $data["callback_query"]["from"]["id"];
//			$employee = Employee::find()->where(["chat_id" => $chat_id])->one();
//			$user = $employee->user;
//			Yii::$app->user->switchIdentity($user, 0);
//			Yii::error(Yii::$app->user->identity->username);
//		}
	}

	public static function start($telegram)
	{
		if ( self::checkPermission($telegram, "employee") ) {
			$result = $telegram->sendMessage([
				'chat_id' => $telegram->input->message->chat->id,
				"text" => "hello",
				"reply_markup" => json_encode([
					"inline_keyboard" => [
						[
							["text" => "Checked?", "callback_data" => "/check"]
						]
					]
				])
			]);
		}
	}
}
>>>>>>> 9c37fa3a038660e6a951c74b648dc2e5ed0e12b1
