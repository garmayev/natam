<?php

namespace frontend\models;

use frontend\behaviors\PhoneNormalizeBehaviors;
use Yii;

/**
 *
 * @property int $id [int(11)]
 * @property string $name [varchar(255)]
 * @property string $phone [varchar(255)]
 * @property string $email [varchar(255)]
 * @property string $company [varchar(255)]
 * @property int $chat_id [int(32)]
 *
 * @property-read Order[] $orders
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
			[["chat_id"], "integer"],
			[["email"], "email"],
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

	public static function findByPhone($mixed)
	{
		return Client::findOne(["phone" => $mixed]);
	}

	public function invite()
	{

	}

	public function getOrders()
	{
		return $this->hasMany(Order::className(), ["client_id" => "id"]);
	}
}