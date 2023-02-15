<?php

namespace common\models;

use common\models\staff\Employee;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 *
 * @property int $id [int(11)]
 * @property string $content
 * @property int $chat_id [int(11)]
 * @property int $message_id
 * @property int $status [int(11)]
 * @property int $order_id [int(11)]
 * @property int $order_status [int(11)]
 * @property int $created_at [int(11)]
 * @property int $created_by [int(11)]
 * @property int $updated_at [int(11)]
 * @property int $updated_by [int(11)]
 * @property int $type
 * @property int $level
 *
 * @property Order $order
 * @property User $createdBy
 * @property User $updatedBy
 * @property int $timeElapsed
 */
class TelegramMessage extends ActiveRecord
{
    const STATUS_OPENED = 0;
    const STATUS_CLOSED = 1;

    const LEVEL_INFO = 0;
    const LEVEL_WARNING = 1;

    const TYPE_NOTIFY = 0;
    const TYPE_ALERT = 1;

    public static function tableName()
    {
        return '{{%telegram_message}}'; // TODO: Change the autogenerated stub
    }

    public static function findByOrder($order_id)
    {
        return self::findAll(['order_id' => $order_id]);
    }

    public static function findByChat_id($chat_id)
    {
        return self::findAll(['chat_id' => $chat_id]);
    }

    public static function send($employee, Order $order, $level = null)
    {
        try {
            if (isset($employee) && isset($employee->chat_id)) {
                $response = Yii::$app->telegram->sendMessage([
                    'chat_id' => $employee->chat_id,
                    'text' => $order->generateTelegramText(),
                    "parse_mode" => "HTML",
                    'reply_markup' => json_encode([
                        'inline_keyboard' => $order->generateTelegramKeyboard()
                    ]),
                ]);
                if ($response->ok) {
                    $message = new TelegramMessage([
                        'order_id' => $order->id,
                        'order_status' => $order->status,
                        'message_id' => $response->result->message_id,
                        'content' => $order->generateTelegramText(),
                        'chat_id' => $employee->chat_id,
                        'updated_at' => null,
                        'updated_by' => null,
                    ]);
                    if (!$message->save()) {
                        Yii::error($message->getErrorSummary(true));
                    }
                } else {
                    Yii::error($employee->attributes);
                    Yii::error($response->result);
                }
            } else {
                $employees = Employee::find()->where(['state_id' => 0])->andWhere(['level' => $level])->all();
                // Yii::error(count($employees));
                foreach ($employees as $employee) {
                    if ($employee->chat_id) {
                        $response = Yii::$app->telegram->sendMessage([
                            'chat_id' => $employee->chat_id,
                            'text' => "Заказ #{$order->id} не был никем обработан",
                            "parse_mode" => "HTML",
                            'reply_markup' => json_encode([
                                'inline_keyboard' => [
                                    [
                                        "text" => "Принято",
                                    ]
                                ]
                            ]),
                        ]);
                        if ($response->ok) {
                            $message = new TelegramMessage([
                                'order_id' => $order->id,
                                'order_status' => $order->status,
                                'message_id' => 1,
                                'content' => "Заказ #{$order->id} не был никем обработан",
                                'chat_id' => $employee->chat_id,
                                'updated_at' => null,
                                'updated_by' => null,
                                'type' => 1,
                                'level' => $level,
                            ]);
                            if (!$message->save()) {
                                Yii::error($message->getErrorSummary(true));
                            }
                        }
                    }
                }
            }
        } catch (ClientException|RequestException $e) {
            Yii::error($e);
        }
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false
            ],
            'blameable' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => false,
            ],
            'saveRelation' => [
                'class' => SaveRelationsBehavior::class,
                'relations' => [
                    'order'
                ]
            ]
        ];
    }

    public
    function rules()
    {
        return [
            [['order_id', 'order_status', 'message_id'], 'required'],
            [['status', 'order_status', 'message_id'], 'integer'],
            [['created_by'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['order_id'], 'exist', 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
            [['status'], 'default', 'value' => self::STATUS_OPENED],
            [['type'], 'default', 'value' => self::TYPE_NOTIFY],
            [['level'], 'default', 'value' => null],
        ];
    }

    public
    function beforeSave($insert)
    {
        if (Yii::$app->user->isGuest) {
            $order = Order::findOne($this->order_id);
            $client = Client::findOne($order->client_id);
            Yii::$app->user->switchIdentity($client->user);
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public
    function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public
    function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public
    function isExpired($time)
    {
        return $time < $this->getTimeElapsed();
    }

    public
    function getTimeElapsed()
    {
        if ($this->status == self::STATUS_CLOSED) {
            return $this->updated_at - $this->created_at;
        } else {
            return false;
        }
    }

    public
    function chain()
    {
        return self::find()
            ->where(['order_id' => $this->order_id])
            ->orderBy('order_status');
    }

    public
    function hide()
    {
        try {
            $response = Yii::$app->telegram->editMessageText([
                'chat_id' => $this->chat_id,
                'message_id' => $this->message_id,
                'text' => Yii::t('app', 'Order #{n} is updated again', ['n' => $this->order_id]),
            ]);
            if ($response->ok) {
                $this->status = self::STATUS_CLOSED;
                $this->updated_by = Yii::$app->user->id;
                $this->updated_at = time();
                $this->save();
            }
        } catch (ClientException $e) {
            Yii::error($this->attributes);
        }
    }

    public
    function beforeDelete()
    {
        try {
            Yii::$app->telegram->send('/deleteMessage', [
                'chat_id' => $this->chat_id,
                'message_id' => $this->message_id,
            ]);
        } catch (ClientException $e) {
            Yii::error($e->getMessage());
        }
        return parent::beforeDelete();
    }
}