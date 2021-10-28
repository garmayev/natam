<?php

namespace frontend\models;

use frontend\behaviors\UpdateBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 *
 * @property int $id [int(11)]
 * @property int $client_id [int(11)]
 * @property string $address [varchar(255)]
 * @property string $comment
 * @property int $status [int(11)]
 * @property int $created_at
 * @property int $updated_at [int(11)]
 * @property int $notify_started_at [int(11)]
 * @property int $boss_chat_id [int(11)]
 *
 * @property int $price
 *
 * @property Client $client
 * @property Product[] $products
 * @property OrderProduct $orderProduct
 * @property Updates[] $updates
 */
class Order extends ActiveRecord
{
	public $tmp_products;

	const STATUS_NEW = 1;
	const STATUS_PREPARE = 2;
	const STATUS_DELIVERY = 3;
	const STATUS_COMPLETE = 4;
	const STATUS_CANCEL = 5;
	const STATUS_HOLD = 6;

	public static function tableName()
	{
		return "{{%order}}";
	}

	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
				],
			], [
				'class' => UpdateBehavior::className(),
				'attribute_name' => 'status',
			]
		];
	}

	public function rules()
	{
		return [
			[["address"], "required"],
			[["address", "comment"], "string"],
			[["client_id"], "integer"],
			[["client_id"], "exist", "targetClass" => Client::className(), "targetAttribute" => "id"],
			[["status"], "default", "value" => self::STATUS_NEW],
			[["notify_started_at"], "default", "value" => 0],
		];
	}

	public function attributeLabels()
	{
		return [
			"address" => Yii::t("app", "Address"),
			"comment" => Yii::t("app", "Comment"),
		];
	}

	private function sendSms()
	{
		$link = "https://telegram.me/natam_trade_bot?start={$this->client->phone}";
		$text = "Подпишитесь на нашего бота, перейдя по ссылке: $link";
		Sms::send($text, $this->client->phone);
	}

	public function getStatus($status = null)
	{
		$statuses = [
			self::STATUS_NEW => "Новый заказ",
			self::STATUS_PREPARE => "Подготовлен для отправки",
			self::STATUS_DELIVERY => "В процессе доставки",
			self::STATUS_COMPLETE => "Выполнен",
			self::STATUS_CANCEL => "Отменен",
			self::STATUS_HOLD => "Отложен",
		];
		if (is_null($status)) {
			return $statuses;
		} else {
			return $statuses[$status];
		}
	}

	public function getClient()
	{
		return $this->hasOne(Client::className(), ["id" => "client_id"]);
	}

	public function getProducts()
	{
		return $this->hasMany(Product::className(), ["id" => "product_id"])->viaTable(OrderProduct::tableName(), ["order_id" => "id"]);
	}

	public function getPrice()
	{
		$price = 0;
		foreach ($this->products as $product) {
			$price += $this->getCount($product->id) * $product->price;
		}
		return $price;
	}

	public function getCount($product_id)
	{
		$query = Yii::$app->db->createCommand("SELECT `product_count` FROM `order_product` WHERE order_id={$this->id} AND product_id={$product_id}")->queryOne();
		return $query["product_count"];
	}

	public function getOrderProduct()
	{
		return $this->hasMany(OrderProduct::className(), ["order_id" => "id"]);
	}

	public function getUpdates()
	{
		return $this->hasMany(Updates::className(), ["order_id" => "id"])->orderBy(["created_at" => SORT_ASC]);
	}

	/**
	 * @param Order $original
	 */
	public function deepClone()
	{
		$db = Yii::$app->db;
		$transaction = $db->beginTransaction();
		try {
			$db->createCommand()->insert('order', [
				"client_id" => $this->client_id,
				'address' => $this->address,
				'status' => self::STATUS_NEW,
			])->execute();
			$newOrderId = $db->getLastInsertID();
			foreach ($this->orderProduct as $orderProduct) {
				$db->createCommand()->insert('order_product', [
					"product_id" => $orderProduct->product_id,
					"product_count" => $orderProduct->product_count,
					"order_id" => $newOrderId,
				])->execute();
			}
		} catch (\Exception $e) {
			$transaction->rollBack();
			Yii::error($e);
		}
		$transaction->commit();
		return Order::findOne($newOrderId);
	}
}