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
 *
 * @property int $price
 *
 * @property Client $client
 * @property-read Product[] $products
 * @property OrderProduct $orderProduct
 * @property Updates[] $updates
 * @property int $notify_started_at [int(11)]
 */
class Order extends ActiveRecord
{
	const STATUS_NEW = 0;
	const STATUS_PREPARE = 1;
	const STATUS_DELIVERY = 2;
	const STATUS_COMPLETE = 3;
	const STATUS_CANCEL = 4;

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
		$client = new \yii\httpclient\Client();
		$link = "https://telegram.me/natam_trade_bot?start={$this->client->phone}";
		$text = "Подпишитесь на нашего бота, перейдя по ссылке: $link";
		$response = $client->createRequest()
			->setMethod("GET")
			->setData([
				"method" => "push_msg",
				"key" => "rRS612f6e0fc5a4d8e633c82af65b5b23a67ee072587ac10",
				"phone" => $this->client->phone,
				"text" => $text,
				"sender_name" => "Natam Trade",
				"format" => "json",
			])
			->setUrl("http://api.sms-prosto.ru/")
			->send();
		if ($response->isOk) {
			Yii::error($response->content);
		}
	}

	public function getStatus($status = null)
	{
		$statuses = [
			self::STATUS_NEW => "Новый заказ",
			self::STATUS_PREPARE => "Подготовлен для отправки",
			self::STATUS_DELIVERY => "В процессе доставки",
			self::STATUS_COMPLETE => "Выполнен",
			self::STATUS_CANCEL => "Отменен",
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
}