<?php

namespace frontend\models;

/**
 *
 * @property int $id [int(11)]
 * @property int $order_id [int(11)]
 * @property int $per_time [int(11)]
 * @property int $created_at [int(11)]
 * @property int $updated_at [int(11)]
 * @property int $message_id [int(11)]
 * @property int $message_timestamp [int(11)]
 * @property int $order_status [int(11)]
 * @property int $staff_id [int(11)]
 *
 * @property Staff $staff
 */
class Updates extends \yii\db\ActiveRecord
{
	public static function tableName()
	{
		return "{{%updates}}";
	}

	public function getStaff() {
		return $this->hasOne(Staff::className(), ["user_id" => "staff_id"]);
	}
}