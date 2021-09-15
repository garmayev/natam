<?php

namespace frontend\models;

use dektrium\user\models\User;

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
		return $states[$this->state];
	}

	public function getUser()
	{
		return $this->hasOne(User::className(), ["id" => "user_id"]);
	}
}