<?php

namespace console\controllers;

use console\models\Alert;
use frontend\models\Order;
use frontend\models\OrderProduct;
use frontend\models\Staff;
use frontend\models\Telegram;
use frontend\models\Updates;
use yii\httpclient\Client;

class NotifyController extends \yii\console\Controller
{
	public function actionIndex()
	{
		$now = time();
		echo "Выбор всех открытых заказов\n";
		$orders = Order::find()->where(["<>", "status", Order::STATUS_COMPLETE])->andWhere(["<>", "status", Order::STATUS_CANCEL])->all();
		echo "Открытых заказов: " . count($orders) . "\n";
		foreach ($orders as $order) {
			$update = Updates::find()->where(["order_id" => $order->id])->orderBy(["created_at" => SORT_DESC, "order_status" => SORT_ASC])->one();
			if (isset($update)) {
				echo "Заказ #{$order->id} был кому-то отправлен ранее\n";
				if (($now - $update->created_at) > \Yii::$app->params["notify"]["limit"][$order->status]) {
					echo "Сотрудник не ответил вовремя\n";
					echo $this->lockMessage($order->id);
					echo $this->next($order->id);
				}
			} else {
				echo $this->next($order->id, true);
			}
		}
		return 0;
	}

	/**
	 * Отправка сообщения коллеге
	 *
	 * @param $order_id
	 * @param bool|false $new
	 * @return int
	 */
	private function next($order_id, $new = false)
	{
		echo "\tПеренаправление сообщения о заказе #{$order_id} другому сотруднику\n";
		$order = Order::findOne($order_id);
		if (empty($order)) {
			return "\tОтправка сообщения не удалась: Неизвестный номер заказа #$order_id\n";
		}
		$staff = Staff::find()->where(["state" => $order->status])->orderBy(["last_message_at" => SORT_ASC])->one();
		if (empty($staff)) {
			return "\tОтправка сообщения не удалась: Нет требуемых кадров на этапе '{$order->getStatus($order->status)}'\n";
		} else if (is_null($staff->chat_id)) {
			return "\tСотрудник '{$staff->user->username}' не установил соединения с Telegram-каналом\n";
		}
		if (($order->notify_started_at !== $staff->user_id)) {
			echo "\t\tОтправка сообщения другому сотруднику\n";
			$text = $this->generateHeader($order);
			$text .= $this->generateProductList($order);
			$keyboard = json_encode($this->generateKeyboard($order));
			$response = Telegram::sendMessage(["chat_id" => $staff->chat_id, "text" => $text, "reply_markup" => $keyboard, "parse_mode" => "markdown"]);
			if ($response->isOk) {
				echo "\t\tОтправка сообщения прошла успешно\n";
				$staff->last_message_at = time();
				$staff->save();
				$this->order_update($order, $staff, $response);
				return 0;
			} else {
				echo "\t\tПри отправке соощения произошли ошибки!\nСмотрите файл console/runtime/logs/app.log\n";
				\Yii::error($response->getData());
				return 1;
			}
		} else {
			echo "\t\tОтправка сообщения Начальству\n";
			return $this->alert($order_id);
		}
	}

	/**
	 * Отправка уведомления о нерадивости сотрудников начальству
	 *
	 * @param $order_id
	 * @return int
	 */
	private function alert($order_id)
	{
		$this->lockMessage($order_id);
		$order = Order::findOne($order_id);
		$time = (!is_null($order->updated_at)) ? $order->updated_at : $order->created_at;
		$boss = Alert::findChat(time() - $time);
		$response = Telegram::sendMessage(["chat_id" => $boss["chat_id"], "text" => "Заказ #{$order->id} никто не обработал со стадии {$order->getStatus($order->status)}"]);
		if ($response->isOk) {
			echo "\t\tОтправка сообщения начальству прошла успешно\n";
			return 0;
		} else {
			echo "\t\tОтправка сообщения не удалась!\nСмотрите файл console/runtime/logs/app.log\n";
			\Yii::error($response->getData());
			return 1;
		}
	}

	/**
	 * Создание обновления в БД
	 *
	 * @param Order $order
	 * @param Staff $staff
	 * @param $response
	 *
	 * @return void
	 */
	private function order_update(Order $order, Staff $staff, $response)
	{
		$body = $response->getData();
		if (is_null($order->notify_started_at)) {
			$order->notify_started_at = $staff->user_id;
			$order->save();
		}
		$update = new Updates([
			"order_id" => $order->id,
			"order_status" => $order->status,
			"staff_id" => $staff->id,
			"created_at" => time(),
			"message_id" => $body["result"]["message_id"],
			"message_timestamp" => $body["result"]["date"],
		]);
		$update->save();
	}

	/**
	 * Блокировка старых сообщений
	 *
	 * @param $order_id
	 * @return int
	 */
	private function lockMessage($order_id)
	{
		$order = Order::findOne($order_id);
		if (empty($order)) {
			return "\tБлокировка не удалась: Неизвестный номер заказа #$order_id\n";
		}
		$staff = Staff::find()->where(["state" => $order->status])->orderBy(["last_message_at" => SORT_DESC])->one();
		if (empty($staff)) {
			$this->alert($order_id);
			return "\tБлокировка не удалась: Нет требуемых кадров\n";
		} else if (is_null($staff->chat_id)) {
			return "\tСотрудник '{$staff->user->username}' не установил соединения с Telegram-каналом\n";
		}
		$update = Updates::find()->where(["order_id" => $order->id])->andWhere(["staff_id" => $staff->id])->orderBy(["created_at" => SORT_DESC])->one();
		if (isset($update)) {
			$response = Telegram::editMessage(["chat_id" => $staff->chat_id, "text" => "Заказ #{$order->id} был переадресован вашему коллеге или начальству", "message_id" => $update->message_id]);
			if ($response->isOk) {
				return "Блокировка прошла успешно\n";
			} else {
				\Yii::error($response->getData());
				return "При блокировке произошли ошибки\nСмотрите файл console/runtime/logs/app.log\n";
			}
		}
	}

	/**
	 * Подготовка заголовка для текстового сообщения в Telegram
	 *
	 * @param Order $order
	 * @return string
	 */
	private function generateHeader($order)
	{
		$text = "";
		switch ($order->status) {
			// Новый заказ (заказ отправляется менеджерам)
			case 0:
				$text .= "Новый заказ #{$order->id}\n\n";
				break;
			// Подготовлен для отправки (заказ отправляется кладовщикам)
			case 1:
				$text .= "Заказ #{$order->id} одобрен\n\n";
				break;
			// В процессе доставки (заказ отправляется водителям)
			case 2:
				$text .= "Заказ #{$order->id} ожидаеь доставки\n\n";
				break;
		}
		return $text;
	}

	/**
	 * Подготовка списка продуктов в заказе для текстового сообщения в Telegram
	 *
	 * @param Order $order
	 * @return string
	 */
	private function generateProductList($order)
	{
		/**
		 * @var OrderProduct $order_product
		 */
		$text = "Список заказанных продуктов: \n";
		$total_price = 0;
		foreach ($order->orderProduct as $order_product) {
			$product = $order_product->product;
			$text .= "\t{$product->title}\n" .
				"\t\tОбъем: {$product->value}\n" .
				"\t\tКоличество: {$order_product->product_count}\n" .
				"\t\tЦена: {$product->price}";
			$total_price += $order_product->product_count * $product->price;
		}
		$text .= "Общая стоимость заказа: {$total_price}";
		return $text;
	}

	/**
	 * Подготовка кнопок для текстового сообщения в Telegram
	 *
	 * @param $order
	 * @return array
	 */
	private function generateKeyboard($order)
	{
		return ["inline_keyboard" => [[
			["text" => "Complete", "callback_data" => "/order_complete id={$order->id}"],
//			["text" => "Cancel", "callback_data" => "/order_cancel id={$order->id}"]
		]]];
	}

	public function actionAlert()
	{
//		var_dump();
	}
}