<?php

namespace common\models;

use common\models\User;
use frontend\models\Telegram;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;

/**
 *
 * @property int $id [int(11)]
 * @property string $name [varchar(255)]
 * @property string $phone [varchar(255)]
 * @property string $email [varchar(255)]
 * @property string $company [varchar(255)]
 * @property int $chat_id [int(32)]
 * @property int $user_id
 *
 * @property-read Order[] $orders
 * @property User $user
 *
 * @property string $inn [varchar(255)]
 * @property string $bik [varchar(255)]
 * @property string $kpp [varchar(255)]
 * @property string $ogrn [varchar(255)]
 * @property string $address [varchar(255)]
 * @property int $location_id [int(11)]
 * @property int $type [int(11)]
 * @property int $notify [int(11)]
 */
class Client extends \yii\db\ActiveRecord
{
	const NOTIFY_NONE = 0;
	const NOTIFY_SMS = 1;
	const NOTIFY_EMAIL = 2;
	const NOTIFY_TELEGRAM = 3;

	public function behaviors()
	{
		return array_merge(parent::behaviors(), [
			'relations' => [
				'class' => SaveRelationsBehavior::className(),
				'relations' => [
					'user'
				]
			]
		]);
	}

	public static function tableName()
	{
		return "{{%client}}";
	}

	public function rules()
	{
		return [
			[["name", "phone"], "required"],
			[["name", "phone", "company"], "string"],
			[["phone"], "unique"],
			[["chat_id", "user_id", "notify"], "integer"],
			[["email"], "email"],
			[["user_id"], "exist", "targetClass" => User::class, "targetAttribute" => "id"],
			[["notify"], "default", "value" => self::NOTIFY_NONE],
			[['user'], 'safe']
		];
	}

	public function beforeSave($insert)
	{
		$valid = parent::beforeSave($insert);
		if ($valid) {
			$user = User::findOne(['username' => $this->phone]);
			if (isset($user)) {
				$this->user = $user;
			} else {
				$user = Yii::createObject([
					'class' => User::className(),
					'scenario' => 'register',
					'username' => $this->phone,
					'email' => ($this->email) ? $this->email : "{$this->phone}@client.com",
					'password' => $this->phone,
				]);
				if ($user->save()) {
					$this->sendNotify("Your login: $this->phone\nYour password: $this->phone", self::NOTIFY_SMS);
					$auth = Yii::$app->authManager;
					$role = $auth->getRole('person');
					$auth->assign($role, $user->id);
					$user->profile->name = $this->name;
					$user->profile->public_email = ($this->email) ? $this->email : "{$this->phone}@client.com";
					$this->user = $user;
					return $valid && $user->profile->save();
				} else {
					Yii::error($user->getErrorSummary(true));
				}
			}
		}
		return $valid;
	}

	public function sendNotify($message, $notify_type)
	{
		if (isset($notify_type)) {
			$notify = $notify_type;
		} else {
			$notify = $this->notify;
		}
		switch ($notify) {
			case self::NOTIFY_SMS:
				$httpClient = new \yii\httpclient\Client();
				$response = $httpClient->createRequest()
					->setUrl("https://sms.ru/sms/send?api_id=F1F520FA-F7CA-4EC7-44C6-71B9D7B07372")
					->setData([
						"to" => "$this->phone",
						"msg" => $message,
						"json" => 1,
					])
					->send();
				if (!$response->isOk) {
					Yii::error($response->getContent());
				}
				return $response->isOk;
			case self::NOTIFY_EMAIL:
				break;
			case self::NOTIFY_TELEGRAM:
				$response = Telegram::sendMessage([
					"chat_id" => $this->chat_id,
					"text" => $message,
				]);
				return $response->isOk;
		}
		return true;
	}

	public function beforeValidate()
	{
		$this->phone = preg_replace("/[\(\)\ \+]*/", "", $this->phone, -1);
		return strlen($this->phone);
	}

	public function attributeLabels()
	{
		return [
			"name" => Yii::t("app", "Name"),
			"phone" => Yii::t("app", "Phone"),
			"email" => Yii::t("app", "Email"),
			"company" => Yii::t("app", "Company"),
		];
	}

	public function setPhone()
	{
		$this->phone = preg_replace("/[\(\)\ \+\-]*/", "", $this->phone, -1);
	}

	public function getFullName()
	{
		return $this->name;
	}

	public static function findByPhone($mixed)
	{
		return Client::findOne(["phone" => preg_replace("/[\(\)\ \+]*/", "", $mixed, -1)]);
	}

	public static function findByChatId($mixed)
	{
		return Client::findOne(["chat_id" => $mixed]);
	}

	public static function findByPhoneOrChatId($mixed)
	{
		$client = self::findByPhone($mixed);
		if (!isset($client)) {
			return self::findByChatId($mixed);
		}
		return $client;
	}

	public function invite()
	{

	}

	public function getOrders()
	{
		return $this->hasMany(Order::className(), ["client_id" => "id"]);
	}

	public function getUser()
	{
		return $this->hasOne(User::className(), ["id" => "user_id"]);
	}

	public function getNotifyList()
	{
		$list = [
			Client::NOTIFY_NONE => "Не отправлять уведомления",
			Client::NOTIFY_SMS => "Уведомлять по SMS",
			Client::NOTIFY_EMAIL => "Уведомлять по E-mail",
		];
		if ($this->chat_id) {
			$list[Client::NOTIFY_TELEGRAM] = "Уведомлять в Telegram";
		}
		return $list;
	}
}