<?php

namespace common\models;

use garmayev\staff\models\Employee;
use garmayev\staff\models\State;
use GuzzleHttp\Exception\ClientException;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 *
 * @property int $id [int(11)]
 * @property int $status [int(11)]
 * @property string $comment
 * @property int $client_id [int(11)]
 * @property int $service_id
 * @property int $created_at [int(11)]
 *
 * @property Client $client
 */
class Ticket extends \yii\db\ActiveRecord
{
	const STATUS_OPEN = 0;
	const STATUS_CLOSE = 1;

	public $require;
	public $phone;
	public $email;

	public static function tableName()
	{
		return "{{%ticket}}";
	}

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
		];
	}

	public function rules()
	{
		return [
			[["comment", "phone"], "string"],
			[["email"], "email"],
			[["status", "service_id", "created_at"], "integer"],
			[["status"], "default", "value" => self::STATUS_OPEN],
			[["phone"], "match", "pattern" => '/^89|79/', "not" => false],
			[["comment"], "match", "pattern" => '/http[s]*:\/\//', "not" => true],
			[["comment"], "match", "pattern" => '/([a-z0-9]*@[a-z0-9\-]*\.[a-z]*)/', "not" => true],
		];
	}

	public function attributeLabels()
	{
		return [
			"name" => Yii::t("app", "Name"),
			"phone" => Yii::t("app", "Phone"),
			"status" => Yii::t("app", "Status"),
			"comment" => Yii::t("app", "Comment"),
			"created_at" => Yii::t("app", "Created At"),
		];
	}

	public function getClient()
	{
		return $this->hasOne(Client::className(), ["id" => "client_id"]);
	}

	public function getStatus($status = null)
	{
		$statuses = [self::STATUS_OPEN => "Открыт", self::STATUS_CLOSE => "Закрыт"];
		if ( is_null($status) ) {
			return $statuses;
		} else {
			return $statuses[$status];
		}
	}

	public function afterSave($insert, $changedAttributes)
	{
		parent::afterSave($insert, $changedAttributes);
//		$this->notifier();
	}

	private function notifier()
	{
		$client = new \yii\httpclient\Client();
		$text = "Клиент <b>{$this->client->name}</b> заказал звонок\n\n";
		if ( ($this->service_id !== 0) && ($this->service_id !== "0") ) {
			$service = Service::findOne($this->service_id);
			$text .= "<b>Услуга, заинтересовавшая клиента</b>: {$service->title}\n";
		}
		if ( $this->comment ) {
			$text .= "<b>Комментарий</b>: {$this->comment}\n";
		}
		$text .= "<b>Номер телефона</b>: <a href='tel:+{$this->client->phone}'>{$this->client->phone}</a>\n";
		if ( $this->client->email ) {
			$text .= "<b>E-mail</b>: {$this->client->email}\n";
		}
		if ( $this->client->company ) {
			$text .= "<b>Компания</b>: {$this->client->company}\n";
		}
		$text .= "<b>Дата создания</b>: ".Yii::$app->formatter->asDatetime($this->created_at);
		preg_match('/http[s]*:\/\//m', $this->comment, $matches);
		if ( count($matches) === 0 ) {
			$employees = Employee::find()->where(['state_id' => 0])->orWhere(['state_id' => 1])->all();
			foreach ($employees as $employee) {
				try {
					Yii::$app->telegram->sendMessage([
						'chat_id' => $employee->chat_id,
						'text' => $text,
						"parse_mode" => "HTML",
					]);
				} catch (ClientException $e) {
					Yii::error($e);
				}
			}
		}
	}
}