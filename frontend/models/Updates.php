<?php

namespace frontend\models;

use common\models\Order;
use garmayev\staff\models\Employee;
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
 * @property int $employee_id [int(11)]
 * @property string $type
 * @property int $boss_id
 *
 * @property Employee $employee
 * @property Order $order
 * @property Employee $chef
 */
class Updates extends ActiveRecord
{
	const TYPE_EMPLOYEE = "employee";
	const TYPE_CHEF = "chef";

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

	public function getEmployee() {
		return $this->hasOne(Employee::className(), ["id" => "employee_id"]);
	}

	public function getOrder()
	{
		return $this->hasOne(Order::className(), ["id" => "order_id"]);
	}

	public function getChef()
	{
		return $this->hasOne(Employee::className(), ["id" => "boss_id"]);
	}
}