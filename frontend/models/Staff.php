<?php

namespace frontend\models;

use common\models\User;

/**
 *
 * @property int $id [int(11)]
 * @property int $user_id [int(11)]
 * @property int $state [int(11)]
 * @property string $phone [varchar(255)]
 * @property int $chat_id [int(11)]
 * @property int $last_message_at [int(11)]
 *
 * @property User $user
 */
class Staff extends \yii\db\ActiveRecord
{
	const STATE_MANAGER = 0;
	const STATE_STORE = 1;
	const STATE_DRIVER = 2;

	public function afterSave($insert, $changedAttributes)
	{
		if ( $insert ) {
			if ( isset($this->phone) ) $this->sendLink();
		}
		parent::afterSave($insert, $changedAttributes);
	}

	public function sendLink()
	{
		$link = "https://telegram.me/natam_trade_bot?start={$this->phone}";
		$text = "Подпишитесь на нашего бота, перейдя по ссылке: $link";
		Sms::send($text, $this->phone);
	}

	public function rules()
	{
		return [
			[["user_id"], "required"],
			[["user_id", "state", "chat_id"], "integer"],
			[["phone"], "string"],
			[["user_id"], "exist", "targetClass" => User::className(), "targetAttribute" => "id"],
			[["state"], "default", "value" => self::STATE_DRIVER],
		];
	}

	public function attributeLabels()
	{
		return [
			"state" => \Yii::t("app", "State"),
			"phone" => \Yii::t("app", "Staff`s Phone"),
		];
	}

	public static function stateLabels($state = null)
	{
		$states = [
			self::STATE_MANAGER => \Yii::t("app", "Manager"),
			self::STATE_STORE => \Yii::t("app", "Store"),
			self::STATE_DRIVER => \Yii::t("app", "Driver"),
		];
		if ( isset($state) ) {
			return $states[$state];
		}
		return $states;
	}

	public function getStateLabel($state = null)
	{
		$states = [
			self::STATE_MANAGER => \Yii::t("app", "Manager"),
			self::STATE_STORE => \Yii::t("app", "Store"),
			self::STATE_DRIVER => \Yii::t("app", "Driver"),
		];
		if ( isset($state) ) {
			return $states[$state];
		}
		if ( !is_null($this->state) ) {
			return $states[$this->state];
		}
		return $states;
	}

	public function getUser()
	{
		return $this->hasOne(User::className(), ["id" => "user_id"]);
	}
}