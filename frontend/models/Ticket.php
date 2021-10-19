<?php

namespace frontend\models;

use garmayev\staff\models\Employee;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;
use yii\db\Expression;

/**
 *
 * @property int $id [int(11)]
 * @property int $status [int(11)]
 * @property string $comment
 * @property int $client_id [int(11)]
 * @property int $service_id
 *
 * @property Client $client
 */
class Ticket extends \yii\db\ActiveRecord
{
	const STATUS_OPEN = 0;
	const STATUS_CLOSE = 1;

	public static function tableName()
	{
		return "{{%ticket}}";
	}

	public function rules()
	{
		return [
			[["comment"], "string"],
			[["status", "service_id"], "integer"],
			[["status"], "default", "value" => self::STATUS_OPEN],
		];
	}

	public function attributeLabels()
	{
		return [
			"name" => Yii::t("app", "Name"),
			"phone" => Yii::t("app", "Phone"),
			"status" => Yii::t("app", "Status"),
			"comment" => Yii::t("app", "Comment"),
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
		$this->notifier();
	}

	private function notifier()
	{
		$client = new \yii\httpclient\Client();
		$text = "Клиент {$this->client->name} заказал звонок на номер {$this->client->phone}\n";
		if ( $this->service_id !== 0 ) {
			$service = Service::findOne($this->service_id);
			$text .= "Услуга, заинтересовавшая клиента: ".$service->title;
		}
		$bot_id = Yii::$app->params["telegram"]["bot_id"];
		$employee = Employee::find()->select(["minValue" => new Expression("MIN(last_message_at)")])->one();
			$response = $client->createRequest()
				->setMethod("POST")
				->setData(["chat_id" => $employee->phone, "text" => $text, "parse_mode" => "markdown"])
				->setUrl("https://api.telegram.org/bot{$bot_id}/sendMessage")
				->send();
	}
}