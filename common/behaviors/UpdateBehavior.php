<?php

namespace common\behaviors;

use common\models\Client;
use common\models\Order;
use common\models\OrderProduct;
use frontend\models\Staff;
use frontend\models\Telegram;
use frontend\models\Updates;
use garmayev\staff\models\Employee;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class UpdateBehavior extends \yii\base\Behavior
{
	public $attribute_name = null;

	public function events()
	{
		return [
			ActiveRecord::EVENT_BEFORE_INSERT => "beforeInsert",
			ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
			ActiveRecord::EVENT_BEFORE_UPDATE => "beforeUpdate",
			ActiveRecord::EVENT_AFTER_UPDATE => "afterUpdate",
		];
	}

	public function saveProducts()
	{
		/**
		 * @var $owner Order
		 * @var $item OrderProduct
		 */
		$owner = $this->owner;
		$dirty = $owner->getDirtyAttributes();
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

	private function diff($hash, $needle)
	{
		$result = [ "mustInsert" => [], "mustDelete" => [] ];
		for ( $i = 0; $i < count($hash["id"]); $i++ ) {
			if ( array_search($hash["id"][$i], array_column($needle, 'product_id')) === false ) {
				$result["mustInsert"]["id"][] = $hash["id"][$i];
				$result["mustInsert"]["count"][] = $hash["count"][$i];
			}
		}
		for ( $i = 0; $i < count($needle); $i++ ) {
			if ( array_search($needle[$i]["product_id"], $hash["id"]) === false ) {
				$result["mustDelete"][] = $needle[$i]["product_id"];
			}
		}
		return $result;
	}

	public function exclude()
	{
		/**
		 * @var OrderProduct $orderProducts
		 * @var Order $owner
		 */
		$owner = $this->owner;
		$post = \Yii::$app->request->post();
		$dirty = $owner->getDirtyAttributes();
		$orderProducts = OrderProduct::find()->where(["order_id" => $owner->id])->asArray()->all();
		$diff = $this->diff($post["Order"]["product"], $orderProducts);
		if ( count($diff["mustDelete"]) ) {
			$orderProducts = OrderProduct::find()->where(["product_id" => $diff["mustDelete"]])->all();
			foreach ($orderProducts as $op) {
				$op->delete();
			}
		}

		if ( count($diff["mustInsert"]) ) {
			for ($i = 0; $i < count($diff["mustInsert"]["id"]); $i++) {
				$op = new OrderProduct([
					"order_id" => $owner->id,
					"product_id" => $diff["mustInsert"]["id"][$i],
					"product_count" => $diff["mustInsert"]["count"][$i]
				]);
				$op->save();
			}
		}
	}

	public function afterInsert($event)
	{
		$this->exclude();
	}

	public function beforeInsert($event)
	{
		/**
		 * @var $owner Order
		 */
	}

	public function beforeUpdate($event)
	{
//		$this->saveProducts();
//		$this->exclude();
//		if ( isset($this->owner->getDirtyAttributes()["status"]) ) $this->owner->updated_at = time();
		if ( $this->owner->{$this->attribute_name} >= 0 && $this->owner->{$this->attribute_name} <= 2 ) {
		}
	}

	public function afterUpdate()
	{
		$this->exclude();
		if ( $this->owner->{$this->attribute_name} >= 0 && $this->owner->{$this->attribute_name} <= 2 ) {
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
}