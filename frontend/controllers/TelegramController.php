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
					$text = "Список ваших заказов";
					$text .= (count($client->orders) === 0) ? " пуст" : ":";
					$markup = ["inline_keyboard" => []];
					foreach (Order::find()->where(["client_id" => $client->id])->andWhere(["<", "status", Order::STATUS_COMPLETE])->all() as $order) {
						$markup["inline_keyboard"][] = [["text" => "Заказ #$order->id", "callback_data" => "/order $order->id"]];
					}
					if ( Telegram::editMessage(["chat_id" => $client->chat_id, "text" => $text, "reply_markup" => json_encode($markup), "message_id" => $telegram->callback_query["message"]["message_id"]]) ) {
						return ["ok" => true];
					}
					break;
				case "/order_complete":
					parse_str($args[0], $argument);
					$order = Order::findOne($argument["id"]);
					$staff = Employee::find()->where(["chat_id" => $telegram->callback_query["from"]["id"]])->one();
					if ( $staff->state === $order->status ) {
						$order->status++;
						$order->save();
					}
					break;
			}
		}
		return ["ok" => false];
	}
}