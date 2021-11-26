<?php

namespace common\behaviors;

use common\models\Client;
use common\models\Order;
use common\models\OrderProduct;
use frontend\models\Staff;
use frontend\models\Telegram;
use frontend\models\Updates;
use yii\db\ActiveRecord;

class UpdateBehavior extends \yii\base\Behavior
{
	public $attribute_name = null;

	public function events()
	{
		return [
			ActiveRecord::EVENT_BEFORE_INSERT => "beforeInsert",
			ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
			ActiveRecord::EVENT_BEFORE_UPDATE => "beforeUpdate",
		];
	}

	public function saveProducts()
	{
		/**
		 * @var $owner Order
		 * @var $item OrderProduct
		 */
		$owner = $this->owner;
		if ( count($_POST) > 0 ) {
			$post = \Yii::$app->request->post();
			if (isset($post["Order"]) && isset($post["Order"]["product"])) {
				for ($i = 0; $i < count($post["Order"]["product"]["id"]); $i++) {
					$op = new OrderProduct([
						"order_id" => $owner->id,
						"product_id" => $post["Order"]["product"]["id"][$i],
						"product_count" => $post["Order"]["product"]["count"][$i],
					]);
					$op->save();
				}
			}

			$tmp = $owner->tmp_products;
			if (!empty($tmp)) {
				foreach ($tmp as $item) {
					$op = new OrderProduct([
						"order_id" => $owner->id,
						"product_id" => $item->product_id,
						"product_count" => $item->product_count,
					]);
					$op->save();
				}
			}
		}
	}

	public function afterInsert($event)
	{
		$this->saveProducts();
//		$sendMessage = $this->sendNewMessage();
//		if (!$sendMessage["ok"]) {
//			\Yii::error($sendMessage["message"]);
//		}
	}

	public function beforeInsert($event)
	{
		/**
		 * @var $owner Order
		 */
//		$owner = $this->owner;
//		$staff = Eployee::find()->where(["state_id" => $this->owner->{$this->attribute_name}])->orderBy(["last_message_at" => SORT_ASC])->one();
//		$owner->notify_started_at = $staff->user_id;
	}

	public function beforeUpdate($event)
	{
		$this->saveProducts();
		if ( isset($this->owner->getDirtyAttributes()["status"]) ) $this->owner->updated_at = time();
		if ( $this->owner->{$this->attribute_name} >= 0 && $this->owner->{$this->attribute_name} <= 2 ) {
//			$disable = $this->disablePreviousMessage($event);
//			if (!$disable["ok"]) {
//				\Yii::error($disable["message"]);
//			}
//			$sendMessage = $this->sendNewMessage();
//			if (!$sendMessage["ok"]) {
//				\Yii::error($sendMessage["message"]);
//			}
		}
	}

	public function disablePreviousMessage($event)
	{
		/**
		 * @var $owner Order
		 */
		$owner = $this->owner;
		$update = Updates::find()->where(["order_id" => $owner->getOldAttributes()["id"]])->orderBy(['created_at' => SORT_DESC])->one();
		if (!empty($update)) {
			$response = (Telegram::editMessage([
				"chat_id" => $update->staff->chat_id,
				"message_id" => $update->message_id,
				"text" => "Заказ #{$update->order->id} был передан вашему коллеге.",
			]))->getData();
			if ($response["ok"]) {
				$update->per_time = time() - $update->created_at;
				$update->save();

				$staff = $update->staff;
				$staff->last_message_at = $response["result"]["date"];
				if ($staff->save()) {
					return ["ok" => true];
				}
				return ["ok" => false, "message" => $staff->getErrorSummary(true)];
			}
			return ["ok" => false, "message" => $response["description"]];
		}
		return ["ok" => false, "message" => "Missing Updates for order"];
	}

	public function sendNewMessage()
	{
		/**
		 * @var Order $owner
		 */
		// Поиск сотрудника, которому не отправлялось сообщение
		$staff = Employee::find()->where(["state_id" => $this->owner->{$this->attribute_name}])->orderBy(["last_message_at" => SORT_ASC])->one();
		$owner = $this->owner;

		// Создание записи в БД (таблица Updates) о наличии изменения заказа
		$update = new Updates([
			"order_id" => $owner->id,
			"order_status" => $owner->{$this->attribute_name},
			"staff_id" => $staff->id,
		]);

		// Отправка сообщения в Telegram
		$response = (Telegram::sendMessage([
			"chat_id" => $staff->chat_id,
			"text" => $this->generateHeader() . $this->generateClientInfo() . "Адрес доставки: {$owner->address}\n" . $this->generateProductList(),
			"reply_markup" => json_encode([
				"inline_keyboard" => [
					[
						["text" => \Yii::t("app", "Complete"), "callback_data" => "/order_complete id={$owner->id}"],
					]
				]
			])
		]))->getData();
		// Если отправка успешна
		if ($response["ok"]) {
			$update->message_id = $response["result"]["message_id"];
			$update->message_timestamp = $response["result"]["date"];
			$update->save(1);
			// Обновление информации в БД (таблица Staff) об отправке сообщения
			$staff->last_message_at = $response["result"]["date"];
			if ($staff->save()) {
				return ["ok" => true];
			} else {
				return ["ok" => false, "message" => $staff->getErrorSummary(true)];
			}
		}
		return ["ok" => false, "message" => $response["description"]];
	}

	/**
	 * Подготовка заголовка для текстового сообщения в Telegram
	 *
	 * @return string
	 */
	private function generateHeader()
	{
		/**
		 * @var $owner Order
		 */
		$owner = $this->owner;
		switch ($owner->status) {
			// Новый заказ (заказ отправляется менеджерам)
			case 0:
				$text = "Новый заказ #{$owner->id}\n\n";
				break;
			// Подготовлен для отправки (заказ отправляется кладовщикам)
			case 1:
				$text = "Заказ #{$owner->id} подготовлен\n\n";
				break;
			// В процессе доставки (заказ отправляется водителям)
			case 2:
				$text = "Заказ #{$owner->id} ожидает доставки\n\n";
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
	private function generateProductList()
	{
		/**
		 * @var OrderProduct $order_product
		 * @var Order $owner
		 */
		$text = "Список заказанных продуктов: \n";
		$total_price = 0;
		$owner = $this->owner;
		foreach ($owner->orderProduct as $order_product) {
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

	private function generateClientInfo()
	{
		/**
		 * @var Client $client
		 * @var Order $order
		 */
		$order = $this->owner;
		$client = $order->client;
		return "Информация о клиенте:\n\t\tФИО: {$client->name}\n\t\tКонтактный номер: {$client->phone}\n";
	}
}