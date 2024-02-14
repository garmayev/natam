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
 * @property string $address [varchar(255)]
 * @property int $location_id [int(11)]
 * @property int $notify [int(11)]
 * @property int $company_id [int(11)]
 * @property Company $organization
 * @property string $mail
 * @property string $inn [varchar(255)]
 * @property string $bik [varchar(255)]
 * @property string $kpp [varchar(255)]
 * @property string $ogrn [varchar(255)]
 */
class Client extends \yii\db\ActiveRecord
{
	const NOTIFY_NONE = 0;
	const NOTIFY_SMS = 1;
	const NOTIFY_EMAIL = 2;
	const NOTIFY_TELEGRAM = 3;
	private $mail;

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
			[["company_id"], "exist", "targetClass" => Company::class, "targetAttribute" => "id"],
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

	public function getMail()
	{
		if ( isset($this->email) ) {
			return $this->email;
		} else {
			if (isset($this->user->profile->public_email)) {
				return $this->user->profile->public_email;
			}
		}
		if ($this->user) {
			return $this->user->email;
		}
		return null;
	}

	public function getFullName()
	{
		return $this->name;
	}

	public function getOrders()
	{
		return $this->hasMany(Order::className(), ["client_id" => "id"])->orderBy(['id' => SORT_DESC]);
	}

	public function getUser()
	{
		return $this->hasOne(User::className(), ["id" => "user_id"]);
	}

	public function getOrganization()
	{
		return $this->hasOne(Company::class, ['id' => 'company_id']);
	}

	public static function findByPhone($mixed)
	{
		$regex = '/[\(\)\ \+]*/';
		return Client::findOne(["phone" => preg_replace($regex, "", $mixed, -1)]);
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

	public static function findOrCreate($mixed)
	{
		$regex = '/[\(\)\ \+\-]*/';
		if ( strlen($mixed["phone"]) > 4 ) {
			$model = self::findOne(["phone" => preg_replace($regex, '', $mixed["phone"])]);
		} else {
			$model = self::findOne(['id' => preg_replace($regex, '', $mixed["name"])]);
		}
		if (!$model) {
			$model = new Client();
			if ($model->load(["Client" => $mixed]) && $model->save()) {
				return $model;
			} else {
				Yii::error($model->getErrorSummary(true));
			}
		}
		return $model;
	}
}