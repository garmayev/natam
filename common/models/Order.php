<?php

namespace common\models;

use common\behaviors\UpdateBehavior;
use frontend\models\Updates;
use frontend\modules\admin\models\Settings;
use garmayev\staff\models\Employee;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsTrait;
use nhkey\arh\ActiveRecordHistoryBehavior;
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
 * @property int $delivery_date
 * @property int $location_id
 *
 * @property int $price
 *
 * @property Client $client
 * @property Product[] $products
 * @property OrderProduct $orderProduct
 * @property Updates[] $updates
 * @property Location $location
 */
class Order extends ActiveRecord
{
	use SaveRelationsTrait;

	public $name;
	public $locationTitle;
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
			], [
				'class' => SaveRelationsBehavior::class,
				'relations' => [
					'products' => [
						'extraColumns' => function ($model) {
							/**
							 * @var Product $model
							 */
							return [
								'product_count' => $this->orderProduct->product_count
							];
						}
					],
					'location',
				]
			], [
				'class' => ActiveRecordHistoryBehavior::class,
				'manager' => '\nhkey\arh\managers\DBManager',
				'ignoreFields' => [
					'id',
					'address',
					'client_id',
					'location_id',
					'comment',
					'created_at',
					'updated_at',
					'notify_started_at',
					'delivery_date'
				]
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
			[["location_id"], "exist", "targetClass" => Location::class, "targetAttribute" => "id"],
		];
	}

	public function attributeLabels()
	{
		return [
			"address" => Yii::t("app", "Address"),
			"comment" => Yii::t("app", "Comment"),
			"status" => Yii::t("app", "Status"),
			"created_at" => Yii::t("app", "Created At"),
			"delivery_date" => Yii::t("app", "Delivery Date"),
			"price" => Yii::t("app", "Price"),
		];
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

	public function getLocation()
	{
		return $this->hasOne(Location::class, ["id" => "location_id"]);
	}

	/**
	 * @return Order|null
	 * @throws \yii\db\Exception
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

	public function search($params)
	{
	}

	public function checkAlerts()
	{
		$settings = (Settings::findOne(["name" => "notify"]))->getContent()["notify"];
		$modified_time = (isset($this->updated_at)) ? $this->updated_at : $this->created_at;
		$now = time();
		for ($i = 0; $i < count($settings["alert"]) - 1; $i++) {
			$current = $settings["alert"][$i];
			$next = $settings["alert"][$i + 1];
			if ((($now - $modified_time) > $current["time"]) && (($now - $modified_time) < $next["time"])) {
				return $current["chat_id"];
			}
		}
		if ($now - $modified_time > $settings["alert"][count($settings)]["time"]) {
			return $settings["alert"][count($settings)];
		} else {
			return null;
		}
	}

	public function checkEmployee()
	{
		$settings = (Settings::findOne(["name" => "notify"]))->getContent()["notify"];
		$updates = Updates::find()->where(["order_id" => $this->id])->orderBy(["created_at" => SORT_ASC])->one();
		if (isset($updates)) {
			if (time() - $updates->created_at > $settings["limit"][$this->status - 1]) {
				$employee = Employee::find()->where(["state_id" => $this->status])->orderBy(["last_message_at" => SORT_ASC])->one();
				if ($this->notify_started_at !== $employee->id) {
					return $employee;
				}
			}
			return null;
		} else {
			return Employee::find()->where(["state_id" => $this->status])->orderBy(["last_message_at" => SORT_ASC])->one();
		}
	}

	public function generateTelegramText()
	{
		$result = "Заказ #{$this->id}\n\n";
		foreach ($this->products as $product) {
			$result .= "$product->title ($product->value): {$this->getCount($product->id)} шт\n";
		}
		$result .= "\nАдрес доставки: {$this->address}\n\n";
		$result .= "Дата доставки: " . Yii::$app->formatter->asDatetime($this->delivery_date);
		$result .= "Общая стоимость: {$this->getPrice()}";
		return $result;
	}

	public function generateTelegramKeyboard()
	{
		$keyboard = [];
		switch ($this->status) {
			case self::STATUS_NEW:
				$keyboard[] =
					[
						[
							"text" => "Передать заказ кладовщику",
							"callback_data" => "/order_complete id={$this->id}"
						]
					];
				break;
			case self::STATUS_PREPARE:
				$employees = Employee::find()->where(["state_id" => $this->status])->limit(5)->all();
				foreach ($employees as $employee) {
					$keyboard[] = [
						[
							"text" => "{$employee->family} {$employee->name}",
							"callback_data" => "/order_driver order_id={$this->id}&driver_id={$employee->id}",
						]
					];
				}
				break;
			case self::STATUS_DELIVERY:
				$keyboard[] = [
					[
						"text" => "Выполнено",
						"callback_data" => "/order_complete id={$this->id}"
					], [
						"text" => "Отказ",
						"callback_data" => "/order_cancel id={$this->id}"
					]
				];
				break;
		}
		return $keyboard;
	}
}