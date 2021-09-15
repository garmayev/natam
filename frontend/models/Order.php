<?php

namespace frontend\models;

use dektrium\user\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 *
 * @property int $id [int(11)]
 * @property int $client_id [int(11)]
 * @property string $address [varchar(255)]
 * @property string $comment
 * @property int $status [int(11)]
 * @property int $created_at
 *
 * @property int $price
 *
 * @property Client $client
 * @property-read Product[] $products
 * @property OrderProduct $order_product
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
				'class' => 'yii\behaviors\TimestampBehavior',
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
				],
			],
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

	public function afterSave($insert, $changedAttributes)
	{
		if ( method_exists( Yii::$app->request, "post" ) ) {
			$post = Yii::$app->request->post();
		} else {
			$post = [];
		}
		if (count($post) > 0) {
			$ids = $post["Order"]["product"]["id"];
			$count = $post["Order"]["product"]["count"];
			for ($i = 0; $i < count($ids); $i++) {
				$order_product = OrderProduct::find()->where(["order_id" => $this->id]);
				$op = $order_product->andWhere(["product_id" => $ids[$i]])->one();
				if (!$op) {
					$op = new OrderProduct(["order_id" => $this->id, "product_id" => $ids[$i], "product_count" => $count[$i]]);
				} else {
					$op->product_count = $count[$i];
				}
				$op->save();
			}
//			$this->sendSms();
		}
//		$this->notifier();
	}

//	private function notifier()
//	{
//		$client = new \yii\httpclient\Client();
//		$text = "Новый заказ #{$this->id}\nКлиент: {$this->client->name}\nНомер телефона: {$this->client->phone}\nАдрес доставки: {$this->address}\nСтатус: {$this->getStatus($this->status)}\nЗаказ: \n";
//		$price = 0;
//		foreach ($this->order_product as $order_product) {
//			$product = Product::findOne($order_product->product->id);
//			$price += $order_product->product_count * $product->price;
//			$text .= "{$product->title}\n\t\t\tОбъем: {$product->value}\n\t\t\tКоличество: {$order_product->product_count}\n\t\t\tЦена: {$product->price}\n";
//		}
//		$text .= "Общая стоимость заказа: {$price}";
//		$keyboard = json_encode(["inline_keyboard" => [[
//			["text" => "Complete", "callback_data" => "/order_complete id={$this->id}"],
//			["text" => "Cancel", "callback_data" => "/order_cancel id={$this->id}"]]]
//		]);
//		$bot_id = Yii::$app->params["telegram"];
//		$staff = Staff::find();
//		switch ($this->status) {
//			case self::STATUS_NEW:
//				$staff->where(["state" => Staff::STATE_MANAGER]);
//				break;
//			case self::STATUS_PREPARE:
//				$staff->where(["state" => Staff::STATE_STORE]);
//				break;
//			case self::STATUS_DELIVERY:
//				$staff->where(["state" => Staff::STATE_DRIVER]);
//				break;
//		}
//		$staff = $staff->one();
//		if (!is_null($staff)) {
//			Yii::error(json_encode($staff->attributes));
//			$response = $client->createRequest()
//				->setMethod("POST")
//				->setData(["chat_id" => $staff->chat_id, "text" => $text, "parse_mode" => "markdown", "reply_markup" => $keyboard])
//				->setUrl("https://api.telegram.org/bot{$bot_id['bot_id']}/sendMessage")
//				->send();
//			if ($response->isOk) {
//				Yii::error($response->getData());
//				$body = $response->getData();
//				$update = new Updates([
//					"order_id" => $this->id,
//					"order_status" => $this->status,
//					"per_time" => time(),
//					"created_at" => time(),
//					"staff_id" => $staff->user_id,
//					"message_id" => $body["result"]["message_id"],
//					"message_timestamp" => $body["result"]["date"],
//				]);
//				$update->save();
////				$update = Updates::find()->where(["order_id" => $this->id]);
////				$staff->message_id = $body["result"]["message_id"];
////				$staff->message_timestamp = $body["result"]["date"];
////				$staff->save();
//			} else {
//				Yii::error($response->getData());
//			}
//		}
//	}

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
		return $this->hasMany(Product::className(), ["id" => "product_id"])->viaTable("{{%order_product}}", ["order_id" => "id"]);
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

	public function getOrder_product() {
		return $this->hasMany(OrderProduct::className(), ["order_id" => "id"]);
	}

	public function getUpdates()
	{
		return $this->hasMany(Updates::className(), ["order_id" => "id"])->orderBy(["created_at" => SORT_ASC]);
	}
}