<?php

namespace console\controllers;

use console\models\Alert;
use frontend\models\Order;
use frontend\models\OrderProduct;
use frontend\models\Telegram;
use frontend\models\Updates;
use frontend\modules\admin\models\Settings;
use garmayev\staff\models\Employee;
use yii\httpclient\Client;

class NotifyController extends \yii\console\Controller
{
	public function init()
	{
		$settings = Settings::findOne(["name" => "notify"]);
		\Yii::$app->params["notify"] = $settings->getContent()["notify"];
		parent::init();
	}

	public function actionIndex()
	{
		$now = time();
		echo "Выбор всех открытых заказов\n";
		$orders = Order::find()->where(["<", "status", Order::STATUS_COMPLETE])->all();
		echo "Открытых заказов: " . count($orders) . "\n";
		foreach ($orders as $order) {
			$update = Updates::find()->where(["order_id" => $order->id])->orderBy(["created_at" => SORT_ASC, "order_status" => SORT_ASC])->one();
			if (isset($update)) {
				echo "Заказ #{$order->id} был кому-то отправлен ранее\n";
				if (($now - $update->created_at) > \Yii::$app->params["notify"]["limit"][$order->status]) {
					echo "Сотрудник не ответил вовремя\n";
					echo $this->lockMessage($order->id);
				}
			}
			echo $this->next($order->id);
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
		$staffs = Employee::find()->where(["state_id" => $order->status])->orderBy(["last_message_at" => SORT_ASC])->all();
		foreach ($staffs as $item) {
			if (isset($item->chat_id)) {
				$staff = $item;
				echo "\tЗаказ будет передан сотруднику {$staff->family}\n";
				break;
			}
//			print_r($item);
//			next($staffs);
		}
		if (empty($staff)) {
			return "\tОтправка сообщения не удалась: Нет требуемых кадров на этапе '{$order->getStatus($order->status)}'\n";
		} else if (is_null($staff->chat_id)) {
			return "\tСотрудник '{$staff->user->username}' не установил соединения с Telegram-каналом\n";
		}
		if (($order->notify_started_at !== $staff->id)) {
			echo "\t\tОтправка сообщения другому сотруднику\n";
			$text = $this->generateHeader($order);
			$text .= $this->generateClientInfo($order);
			$text .= $this->generateProductList($order);
			$keyboard = json_encode($this->generateKeyboard($order));
			$response = Telegram::sendMessage(["chat_id" => $staff->chat_id, "text" => $text, "reply_markup" => $keyboard, "parse_mode" => "markdown"]);
			if ($response->isOk) {
				echo "\t\tОтправка сообщения прошла успешно\n";
				$staff->last_message_at = time();
				$staff->save();
				$this->order_update($order, $staff, $response);
				// return 0;
			} else {
				echo "\t\tПри отправке соощения произошли ошибки!\nСмотрите файл console/runtime/logs/app.log\n";
				\Yii::error($response->getData());
				\Yii::error($staff->chat_id);
				\Yii::error($text);
				// return 1;
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
		if (is_null($boss)) {
			return 0;
		}
		$response = Telegram::sendMessage(["chat_id" => $boss["chat_id"], "text" => "Заказ #{$order->id} никто не обработал со стадии {$order->getStatus($order->status)}"]);
		if ($response->isOk) {
			echo "\t\tОтправка сообщения начальству прошла успешно\n";
			$order->boss_chat_id = $boss["chat_id"];
			$order->save();
			return 0;
		} else {
			echo "\t\tОтправка сообщения не удалась!\nСмотрите файл console/runtime/logs/app.log\n";
			\Yii::error($response->getData());
			\Yii::error($boss["chat_id"]);
			\Yii::error("Заказ #{$order->id} никто не обработал со стадии {$order->getStatus($order->status)}");
			return 1;
		}
	}

	/**
	 * Создание обновления в БД
	 *
	 * @param Order $order
	 * @param Employee $staff
	 * @param $response
	 *
	 * @return void
	 */
	private function order_update(Order $order, Employee $staff, $response)
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
		$staff = Employee::find()->where(["state_id" => $order->status])->orderBy(["last_message_at" => SORT_DESC])->one();
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
				$update->updated_at = time();
				$update->save();
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

	private function generateClientInfo($order)
	{
		/**
		 * @var $order Order
		 */
		$text = "Информация о клиенте\n";
		if (isset($order->client->name)) {
			$text .= "\tФИО: {$order->client->name}\n";
		}
		if (isset($order->client->phone)) {
			$text .= "Контактный номер: {$order->client->phone}\n\n";
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
				"\t\tЦена: {$product->price}\n\n";
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
			["text" => "Выполнено", "callback_data" => "/order_complete id={$order->id}"],
			["text" => "Отложить", "callback_data" => "/order_hold id={$order->id}"]
		]]];
	}
}
