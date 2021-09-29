<?php

namespace frontend\models;

use yii\db\ActiveRecord;

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
 * @property Order $order
 */
class Updates extends ActiveRecord
{
	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => 'yii\behaviors\TimestampBehavior',
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
				],
			]
		];
	}

	public static function tableName()
	{
		return "{{%updates}}";
	}

	public function getStaff() {
		return $this->hasOne(Staff::className(), ["id" => "staff_id"]);
	}

	public function getOrder()
	{
		return $this->hasOne(Order::className(), ["id" => "order_id"]);
	}
}