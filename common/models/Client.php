<?php

namespace common\models;

use common\models\User;
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
			if ( isset($user) ) {
				$this->user = $user;
			} else {
				$user = Yii::createObject([
					'class' => User::className(),
					'scenario' => 'register',
					'username' => $this->phone,
					'email' => $this->email,
					'password' => $this->phone,
				]);
				if ($user->save()) {
					$auth = Yii::$app->authManager;
					$role = $auth->getRole('person');
					Yii::error($role);
					$auth->assign($role, $user->id);
					$user->profile->name = $this->name;
					$user->profile->public_email = $this->email;
					return $valid && $user->profile->save();
				} else {
					Yii::error($user->getErrorSummary(true));
				}
			}
		}
		return $valid;
	}

	public function sendNotify()
	{
		switch ($this->notify) {
			case self::NOTIFY_SMS:
				$httpClient = new \yii\httpclient\Client();
				$response = $httpClient->createRequest()
					->setUrl("https://platform.clickatell.com/messages/http/send?apiKey=".Yii::$app->params["clickatell"]["apiKey"])
					->setData([
						"to" => "$this->phone",
						"content" => "Your Login: $this->phone\nYour Password: $this->phone",
					])
					->send();
				if ($response->isOk) {
					return true;
				} else {
					Yii::error($response->getContent());
					return false;
				}
				break;
			case self::NOTIFY_EMAIL:
				break;
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
		if ( !isset($client) ) {
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
}