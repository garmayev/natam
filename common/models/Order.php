<?php

namespace common\models;

use common\models\Product;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\staff\Employee;

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
 * @property double $delivery_distance
 * @property int $delivery_type [int(11)]
 * @property int $totalPrice
 * @property int $delivery_city
 *
 * @property Client $client
 * @property Product[] $products
 * @property OrderProduct[] $orderProducts
 * @property Location $location
 * @property TelegramMessage[] $messages
 * @property-read Company $company
 * @property-read mixed $statusName
 * @property string $article [varchar(255)]
 */
class Order extends ActiveRecord
{
    use SaveRelationsTrait;

    public $deliveryPrice;

    const STATUS_NEW = 1;
    const STATUS_PREPARED = 2;
    const STATUS_DELIVERY = 3;
    const STATUS_COMPLETE = 4;
    const STATUS_CANCEL = 5;
    const STATUS_HOLD = 6;

    const DELIVERY_SELF = 0;
    const DELIVERY_STORE = 1;

    const STORE_RESERVED = 1;
    const STORE_SOLD = 2;
    const STORE_CANCELLED = 3;

    const EVENT_TELEGRAM_INSERT = 'event_insert';
    const EVENT_TELEGRAM_UPDATE = 'event_update';

    const SCENARIO_TELEGRAM = 'telegram';

    public $_status;

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => null,
                ]
            ],
            'saveRelation' => [
                'class' => SaveRelationsBehavior::class,
                'relations' => [
                    'client',
                    'location',
                    'products',
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_INSERT | self::OP_UPDATE
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['delivery_date'], 'required'],
            [['client_id', 'location_id', 'delivery_type', 'created_at', 'delivery_city'], 'integer'],
            [['comment', 'article'], 'string'],
            [['delivery_distance'], 'double'],
            [['delivery_distance'], 'default', 'value' => 0],
            [['client_id'], 'exist', 'skipOnError' => false, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['location_id' => 'id']],
            [['status'], 'default', 'value' => self::STATUS_NEW],
            [['delivery_date'], 'filter', 'filter' => function ($value) {
                return Yii::$app->formatter->asTimestamp($value);
            }],
            [['delivery_type'], 'default', 'value' => self::DELIVERY_STORE],
            [['client', 'location', 'products'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'client_id' => Yii::t('app', 'Client ID'),
            'location_id' => Yii::t('app', 'Location ID'),
            'delivery_at' => Yii::t('app', 'Delivery Date'),
            'status' => Yii::t('app', 'Status'),
            'statusName' => Yii::t('app', 'Status'),
            'price' => Yii::t('app', 'Price'),
            'comment' => Yii::t('app', 'Comment'),
            'created_at' => Yii::t('app', 'Created At'),
            'delivery_date' => Yii::t('app', 'Delivery Date'),
            'delivery_city' => Yii::t('app', 'Delivery City'),
        ];
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function load($data, $formName = null)
    {
        $this->save(false);
        $scope = isset($formName) ? $formName : $this->formName();
        if ( isset($data[$scope]["delivery_type"]) ) {
            $data[$scope]["delivery_type"] = ($data[$scope]["delivery_type"] === "on") ? Order::DELIVERY_SELF : Order::DELIVERY_STORE;
        }

        $client = Client::findOrCreate($data[$scope]["client"]);
        $this->client_id = $client->id;

        return parent::load($data, $formName);
    }

    public function fields()
    {
        return [
            'id',
            'status',
            'client' => function () {
                return $this->client;
            },
            'location' => function () {
                return $this->location;
            },
            'statusName' => function () {
                return $this->statusName;
            },
            'created_at' => function () {
                return Yii::$app->formatter->asDatetime($this->created_at);
            },
            'delivery_type' => function () {
                return ($this->delivery_type) ? Yii::t('app', 'Delivery') : Yii::t('app', 'Self delivery');
            },
            'delivery_at' => function () {
                return Yii::$app->formatter->asDatetime($this->delivery_date);
            },
            'store' => function () {
                if (isset($this->store_id)) {
                    return $this->store;
                }
                return "";
            },
            'comment' => function () {
                return $this->comment;
            },
            'price' => function () {
                return $this->getPrice();
            },
            'deliveryPrice' => function () {
                return $this->deliveryPrice;
            },
            'products' => function () {
                $data = [];
                foreach ($this->orderProducts as $orderProduct) {
                    $data[] = [
                        "product" => $orderProduct->product,
                        "count" => $orderProduct->product_count,
                    ];
                }
                return $data;
            }
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
//        Yii::error($changedAttributes);
        if (!$insert) {
//			$messages = TelegramMessage::find()->where(['order_id' => $this->id])->andWhere(['order_status' => $this->status - 1])->andWhere(['status' => TelegramMessage::STATUS_OPENED])->all();
//			Yii::error();
//			foreach ($messages as $message) {
//				$message->hide();
//			}
            if (count($this->orderProducts)) {
                if ($this->status !== Order::STATUS_DELIVERY) {
                    $employees = Employee::find()->where(['state_id' => $this->status])->all();
//					Yii::error(count($this->orderProducts));
                    foreach ($employees as $employee) {
//						\Yii::error($employee->attributes);
//						TelegramMessage::send($employee, $this);
                    }
                }
            }
        }
    }

    /**
     * Model Attributes
     */

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->client->organization;
    }

    /**
     * Gets query for [[Location]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id']);
    }

    /**
     * Set Location for Order model
     *
     * @param $data
     * @return void
     */
    public function setLocation($data)
    {
        $location = Location::findOrCreate($data);
        $this->location_id = $location->id;
    }

    /**
     * Gets query for [[ProductOrders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderProducts()
    {
        return $this->hasMany(OrderProduct::className(), ['order_id' => 'id']);
    }

    public function getPrice()
    {
        $price = 0;
        foreach ($this->orderProducts as $orderProduct) {
            $price += $orderProduct->product->price * $orderProduct->product_count;
        }
        return $price;
    }

    /**
     * Gets query for [[Products]].
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['id' => 'product_id'])->viaTable('order_product', ['order_id' => 'id']);
    }

    public function setProducts($data)
    {
        foreach ($this->orderProducts as $orderProduct) $orderProduct->delete();
        foreach ($data as $item) {
            $product = Product::findOne($item['product_id']);
            $this->link('products', $product, ['product_count' => $item['product_count']]);
        }
    }

    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }

    public function setClient($data)
    {
        $client = Client::findOrCreate($data);
        $this->client_id = $client->id;
    }

    public function getTotalPrice()
    {
        $sum = 0;
        foreach ($this->productOrders as $productOrder) {
            $sum += $productOrder->product->price * $productOrder->product_count;
        }
        return $sum;
    }

    public function getStatusName()
    {
        return Order::getStatusList()[$this->status];
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_NEW => Yii::t('app', 'New Order'),
            self::STATUS_PREPARED => Yii::t('app', 'Order Prepared'),
            self::STATUS_DELIVERY => Yii::t('app', 'Order Delivered'),
            self::STATUS_COMPLETE => Yii::t('app', 'Order Complete'),
            self::STATUS_CANCEL => Yii::t('app', 'Order Cancelled'),
        ];
    }

    public function getCount($product_id)
    {
        foreach ($this->orderProducts as $orderProduct) {
            if ( $orderProduct->product_id === $product_id ) return $orderProduct->product_count;
        }
        return 0;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(TelegramMessage::class, ['order_id' => 'id']);
    }

    public function getDeliveryPrice()
    {
        if ( !$this->delivery_city ) {
            return $this->delivery_distance * Settings::getDeliveryCost();
        }
        return 0;
    }

    public function getMostExpensive($limit)
    {
//		$query = Order::find("*, ")
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
        $result .= "<b>Статус</b>: " . $this->getStatusName() . "\n";
        $delivery_price = 0;
        if ($this->delivery_city !== 1) {
            $delivery_price = intval($this->delivery_distance) * Settings::getDeliveryCost();
            $result .= "<b>Стоимость доставки</b>: {$delivery_price}\n";
        }
        $result .= "<b>ФИО клиента</b>: {$this->client->name}\n<b>Номер телефона</b>: <a href='tel:+{$this->client->phone}'>{$this->client->phone}</a>\n";
        $result .= "<b>Дата доставки</b>: " . Yii::$app->formatter->asDatetime($this->delivery_date) . "\n";
        $result .= "<b>Комментарий</b>: " . $this->comment . "\n";
//		Yii::error($this->getPrice());
//		Yii::error($delivery_price);
        $price = $this->getPrice() + $delivery_price;
        $result .= "<i>Общая стоимость: {$price}</i>";
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
            case self::STATUS_PREPARED:
                if ($this->delivery_type == self::DELIVERY_STORE) {
                    $employees = \garmayev\staff\models\Employee::find()->where(["state_id" => Order::STATUS_DELIVERY])->all();
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
}
