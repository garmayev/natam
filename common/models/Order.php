<?php

namespace common\models;

use aki\telegram\Telegram;
use common\behaviors\NotifyBehavior;
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
 * @property int $hold_at
 * @property int $hold_time
 *
 * @property int $price
 *
 * @property Client $client
 * @property Product[] $products
 * @property OrderProduct[] $orderProducts
 * @property Updates[] $updates
 * @property-write mixed $count
 * @property Location $location
 * @property int $delivery_type [int(11)]
 * @property TelegramMessage[] $messages
 */
class Order extends ActiveRecord
{
//	use SaveRelationsTrait;

	public $name;
	public $locationTitle;
	public $orderProduct;

	const STATUS_NEW = 1;
	const STATUS_PREPARE = 2;
	const STATUS_DELIVERY = 3;
	const STATUS_COMPLETE = 4;
	const STATUS_CANCEL = 5;
	const STATUS_HOLD = 6;

	const DELIVERY_SELF = 0;
	const DELIVERY_COMPANY = 1;

	const SCENARIO_DELIVERY_SELF = 'delivery_self';

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
			],
//			'relations' => [
//				'class' => SaveRelationsBehavior::class,
//				'relations' => [
//					'location',
//					'client',
//				],
//			],
			'rel' => [
				'class' => UpdateBehavior::className(),
			],
//			'notify' => [
//				'class' => NotifyBehavior::class,
//				'attribute' => 'status',
//			]
		];
	}

	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_DEFAULT] = ['!address', 'orderProduct', 'status', 'delivery_type', 'notify_started_at'];
		$scenarios[self::SCENARIO_DELIVERY_SELF] = ['address', 'orderProduct', 'status', 'delivery_type', 'notify_started_at'];
		return $scenarios;
	}

	public function rules()
	{
		return [
			[["address", "comment"], "string"],
			[["client_id"], "integer"],
			[["client_id"], "exist", "targetClass" => Client::className(), "targetAttribute" => "id"],
			[["status"], "default", "value" => self::STATUS_NEW],
			[['delivery_type'], "default", "value" => self::DELIVERY_COMPANY],
			[["notify_started_at"], "default", "value" => 0],
			[["location_id"], "exist", "targetClass" => Location::class, "targetAttribute" => "id"],
			[['orderProducts', 'orderProduct'],'safe'],
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

	public function load($data, $formName = null)
	{
		$parent = parent::load($data, $formName);

		if ( isset($data["Client"]["phone"]) ) {
			$client = Client::findByPhone($data["Client"]["phone"]);
			if (!isset($client)) {
				$client = new Client($data["Client"]);
				$client->save();
			}
			$this->client_id = $client->id;
		}

		if ( !empty($data["Order"]["location"]["title"]) ) {
			$location = Location::findOne(['title' => $data['Order']['location']['title']]);
			if (!isset($location)) {
				$location = new Location($data["Order"]['location']);
				$location->save();
			}
			$this->location_id = $location->id;
			$this->delivery_type = self::DELIVERY_COMPANY;
		} else {
			$this->scenario = self::SCENARIO_DELIVERY_SELF;
			$this->location = null;
			$this->delivery_type = self::DELIVERY_SELF;
		}
		$this->comment = $data["Order"]["comment"];
		return $parent;
	}

	public function afterSave($insert, $changedAttributes)
	{
		parent::afterSave($insert, $changedAttributes);

		$messages = TelegramMessage::find()
			->where(['order_id' => $this->id])
			->andWhere(['status' => TelegramMessage::STATUS_OPENED])
			->all();
		foreach ($messages as $message) $message->hide();

		\Yii::error($insert);
		\Yii::error($changedAttributes);

		if ( !$insert ) {
			if ( isset($changedAttributes['status']) && $this->attributes['status'] < Order::STATUS_DELIVERY ) {
				$employees = Employee::findAll(['state_id' => $this->attributes['status']]);
				foreach ($employees as $employee) TelegramMessage::send($employee, $this);
			}
		} else if (isset($changedAttributes['status'])) {
			$employees = Employee::findAll(['state_id' => $this->status]);
			foreach ($employees as $employee) TelegramMessage::send($employee, $this);
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
			self::STATUS_HOLD => "Отложен",
		];
		if (is_null($status)) {
			return $statuses;
		} else {
			return $statuses[$status];
		}
	}

	public static function getStatusList()
	{
		return [
			self::STATUS_NEW => "Новый заказ",
			self::STATUS_PREPARE => "Подготовлен для отправки",
			self::STATUS_DELIVERY => "В процессе доставки",
			self::STATUS_COMPLETE => "Выполнен",
			self::STATUS_CANCEL => "Отменен",
			self::STATUS_HOLD => "Отложен",
		];
	}

	public function getClient()
	{
		return $this->hasOne(Client::className(), ["id" => "client_id"]);
	}

	public function getProducts()
	{
		return $this->hasMany(Product::className(), ["id" => "product_id"])->via('orderProducts');
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
		if ( $query ) {
			return $query["product_count"];
		}
	}

	public function setCount($product_id)
	{
		$item = Yii::$app->cart->getItem($product_id);
		$query = Yii::$app->db->createCommand("UPDATE `order_product` SET `product_count` = {$item->getQuantity()} WHERE `order_id` = {$this->id} AND `product_id` = {$product_id};")->execute();
		return true;
	}

	public function getOrderProducts()
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
		$result = "<b>Заказ #{$this->id}</b>\n\n";
		foreach ($this->products as $product) {
			$result .= "<strong>$product->title</strong> ($product->value) {$this->getCount($product->id)} * {$product->price}\n";
		}
		$result .= "\n";
		if ($this->delivery_type !== self::DELIVERY_SELF) {
			if ($this->location) {
				$result .= "<b>Адрес доставки</b>: <a href='https://2gis.ru/routeSearch/rsType/car/from/107.683039,51.835453/to/{$this->location->longitude},{$this->location->latitude}/go'>{$this->location->title}</a>\n";
			} else {
				$result .= "<b>Адрес доставки</b>: {$this->address}\n";
			}
		} else {
			$result .= "<b>Адрес доставки</b>: Самовывоз\n";
		}
		$result .= "<b>ФИО клиента</b>: {$this->client->name}\n<b>Номер телефона</b>: <a href='tel:+{$this->client->phone}'>{$this->client->phone}</a>\n";
		$result .= "<b>Дата доставки</b>: " . Yii::$app->formatter->asDatetime($this->delivery_date) . "\n";
		$result .= "<b>Комментарий</b>: " . $this->comment . "\n";
		$result .= "<i>Общая стоимость: {$this->getPrice()}</i>";
		return $result;
	}

	public function generateTelegramKeyboard()
	{
		$keyboard = [];
		switch ($this->status) {
			case self::STATUS_NEW:
				$keyboard[] = [
					[
						"text" => "Передать заказ кладовщику",
						"callback_data" => "/manager id={$this->id}"
					],
				];
				$keyboard[] = [
					 [
						"text" => "Отложить",
						"callback_data" => "/order_hold id={$this->id}"
					]
				];
				break;
			case self::STATUS_PREPARE:
				if ( $this->delivery_type == self::DELIVERY_COMPANY ) {
					$employees = Employee::find()->where(["state_id" => Order::STATUS_DELIVERY])->all();
					foreach ($employees as $employee) {
						$keyboard[] = [
							[
								"text" => "{$employee->family} {$employee->name}",
								"callback_data" => "/store id={$this->id}&driver_id={$employee->id}",
							]
						];
					}
				} else {
					$keyboard[] = [
						[
							"text" => "Выполнено",
							"callback_data" => "/store id={$this->id}",
						]
					];
				}
				break;
			case self::STATUS_DELIVERY:
				$keyboard[] = [
					[
						"text" => "Выполнено",
						"callback_data" => "/driver id={$this->id}"
					]
				];
				break;
		}
		return $keyboard;
	}

	public function hold($time) {
		$this->hold_at = time();
		$this->hold_time = $time;
		$this->save();
	}

	public function getHistory()
	{
		return $this->hasMany(ActiveRecordHistoryBehavior::class, ["field_id" => "id"]);
	}

	public function getMessages()
	{
		return $this->hasMany(TelegramMessage::class, ['order_id' => 'id']);
	}
}
