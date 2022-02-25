<?php

namespace common\models;

use common\behaviors\PhoneNormalizeBehaviors;
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
 */
class Client extends \yii\db\ActiveRecord
{

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
			[["chat_id", "user_id"], "integer"],
			[["email"], "email"],
			[["user_id"], "exist", "targetClass" => User::class, "targetAttribute" => "id"],
		];
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
		$this->phone = preg_replace("/[\(\)\ \+]*/", "", $this->phone, -1);
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