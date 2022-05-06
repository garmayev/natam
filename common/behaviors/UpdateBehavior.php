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
	public function events()
	{
		return [
			ActiveRecord::EVENT_AFTER_INSERT => 'exclude',
			ActiveRecord::EVENT_AFTER_UPDATE => "exclude",
		];
	}

	public function saveProducts()
	{
		/**
		 * @var $owner Order
		 * @var $item OrderProduct
		 */

		$owner = $this->owner;
		foreach ($owner->orderProduct as $product) {
			$link = new OrderProduct([
				"order_id" => $owner->id,
				"product_id" => $product["product_id"],
				"product_count" => $product["product_count"]
			]);
			$link->save();
		}
	}

	public function exclude($event = null)
	{
		/**
		 * @var OrderProduct $orderProducts
		 * @var Order $owner
		 */

		$owner = $this->owner;
		if (isset($_POST["Order"])) {
			$orderProduct = OrderProduct::find()->where(["order_id" => $owner->id])->all();
			foreach ($orderProduct as $product) {
				$product->delete();
			}
			$this->saveProducts();
		}
	}
}
