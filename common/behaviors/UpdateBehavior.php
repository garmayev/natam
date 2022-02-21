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
			ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
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
		if ( count($_POST) > 0 ) {
			$post = \Yii::$app->request->post();
			$orderProducts = OrderProduct::find()->where(["order_id" => $owner->id])->asArray()->all();
			$diff = $this->diff($post["Order"]["product"], $orderProducts);
			if (count($diff["mustDelete"])) {
				$orderProducts = OrderProduct::find()->where(["product_id" => $diff["mustDelete"]])->all();
				foreach ($orderProducts as $op) {
					$op->delete();
				}
			}

			if (count($diff["mustInsert"])) {
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
	}

	public function afterInsert($event)
	{
		$this->exclude();
	}

	public function afterUpdate()
	{
		$this->exclude();
	}
}
