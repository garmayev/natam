<?php

namespace frontend\controllers;

use frontend\models\Client;
use frontend\models\Order;
use frontend\models\OrderProduct;
use frontend\models\Staff;
use frontend\models\Telegram;
use frontend\models\Updates;
use frontend\models\User;
use garmayev\staff\models\Employee;
use Yii;

/**
 *
 */
class TelegramController extends \yii\rest\Controller
{
	public function beforeAction($action)
	{
		$this->enableCsrfValidation = false;
		return parent::beforeAction($action); // TODO: Change the autogenerated stub
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
					$copy->save();
					// $text = "Список ваших заказов";
					// $text .= (count($client->orders) === 0) ? " пуст" : ":";
					// $markup = ["inline_keyboard" => []];
					// foreach (Order::find()->where(["client_id" => $client->id])->andWhere(["<", "status", Order::STATUS_COMPLETE])->all() as $order) {
					// 	$markup["inline_keyboard"][] = [["text" => "Заказ #$order->id", "callback_data" => "/order $order->id"]];
					// }
					// if ( Telegram::editMessage(["chat_id" => $client->chat_id, "text" => $text, "reply_markup" => json_encode($markup), "message_id" => $telegram->callback_query["message"]["message_id"]]) ) {
					// 	return ["ok" => true];
					// }
					break;
				case "/order_complete":
					parse_str($args[0], $argument);
					$order = Order::findOne($argument["id"]);
					$staff = Employee::find()->where(["chat_id" => $telegram->callback_query["from"]["id"]])->one();
					if ( isset($staff) && isset($order) && ($staff->state_id == $order->status) ) {
						$updates = Updates::find()->where(["order_id" => $order->id])->andWhere(["order_status" => $order->status])->andWhere(["updated_at" => null])->all();
						foreach ($updates as $update) {
							Telegram::editMessage(["chat_id" => $update->employee->chat_id, "message_id" => $update->message_id, "text" => "Статус заказа #{$order->id} был изменен"]);
							$update->delete();
						}
						$order->status++;
						$order->save();
						// if ($staff->state_id == $order->status) {
						// 	$order->status++;
						// 	if ( $order->save() ) {
						// 		$update = Updates::find()->where(["message_id" => $telegram->callback_query["message"]["message_id"]])->one();
						// 		if ( isset($update) ) {
						// 			$update->updated_at = time();
						// 			$update->save();
						// 		}
						// 		return ["ok" => true];
						// 	};
						// }
					}
					break;
				case "/order_driver":
					parse_str($args[0], $argument);
					$order = Order::findOne($argument["order_id"]);
					$driver = Employee::findOne($argument["driver_id"]);
					$updates = Updates::find()->where(["order_id" => $order->id])->andWhere(["order_status" => $order->status])->andWhere(["updated_at" => null])->all();
					foreach ($updates as $update) {
						Telegram::editMessage([
							"chat_id" => $update->employee->chat_id,
							"text" => "Стаутус заказа #{$order->id} был изменен",
							"message_id" => $update->message_id
						]);
						$update->delete();
					}

					$text = "Заказ #$order->id\nАдрес доставки: $order->address\nДата создания заказа: ".\Yii::$app->formatter->asDate($order->created_at, "php: d M Y H:i")."\nСодержимое заказа:\n";
					foreach ($order->products as $product) {
						$text .= "\t$product->title\n\t\t$product->value\n\t\t$product->price\n";
					}
					$text .= "\nОбщая стоимость заказа: $order->price";
					$markup = ["inline_keyboard" => [
						[
							[
								"text" => "Выполнено",
								"callback_data" => "/order_complete $order->id"
							]
						], [
							[
								"text" => "Отложить", 
								"callback_data" => "/order_hold id=$order->id"
							]
						]
					]];

					Telegram::sendMessage([
						"chat_id" => $driver->chat_id,
						"text" => $text,
						"reply_markup" => json_encode($markup),
					]);
					break;
				case "/order_hold":
					parse_str($args[0], $argument);
					$order = Order::findOne($argument["id"]);
					$order->status = Order::STATUS_HOLD;
					if ( $order->save() ) {
						$updates = Updates::find()->where(["order_id" => $order->id])->andWhere(["order_status" => $order->status])->andWhere(["updated_at" => null])->all();
						foreach ($updates as $update) {
							Telegram::editMessage([
								"message_id" => $update->message_id,
								"text" => "Заказ #$order->id отложен\nАдрес доставки: $order->address\nДата создания заказа: ".\Yii::$app->formatter->asDate($order->created_at, "php: d M Y H:i")."\nСодержимое заказа:\n", 
								"chat_id" => $update->employee->chat_id,
								"reply_markup" => json_encode(["inline_keyboard" => [
									[["text" => "Кладовщику", "callback_data" => "/order_restore id={$order->id}"]];
								]]),
							]);
							$update->delete();
						}
						return ["ok" => true];
					}
					break;
				case "/order_restore":
					parse_str($args[0], $argument);
					$order = Order::findOne($argument["id"]);
					$staff = Employee::find()->where(["chat_id" => $telegram->callback_query["from"]["id"]])->one();
					if ( isset($staff) && isset($order) && ($staff->state_id == $order->status) ) {
						$updates = Updates::find()->where(["order_id" => $order->id])->andWhere(["order_status" => $order->status])->andWhere(["updated_at" => null])->all();
						foreach ($updates as $update) {
							Telegram::editMessage(["chat_id" => $update->employee->chat_id, "message_id" => $update->message_id, "text" => "Статус заказа #{$order->id} был изменен"]);
							$update->delete();
						}
						$order->status = Order::STATUS_PREPARE;
						$order->save();
					}
					break;
			}
		}
		return ["ok" => false];
	}
}