<?php

namespace common\behaviors;

use common\models\Order;
use common\models\TelegramMessage;
use garmayev\staff\models\Employee;
use yii\db\ActiveRecord;

class NotifyBehavior extends \yii\base\Behavior
{
	public $attribute;

	public function events()
	{
		return [
			ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
			ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
		];
	}

	public function afterInsert($event)
	{
		/**
		 * @var Order $owner
		 */
		$owner = $this->owner;
		$employees = Employee::find()
			->where(['state_id' => $event->changedAttributes[$this->attribute]])
			->all();
		if (count($employees)) {
			foreach ($employees as $employee) TelegramMessage::send($employee, $owner);
		}
	}

	public function afterUpdate($event)
	{
		/**
		 * @var Order $owner
		 */
		$owner = $this->owner;
		\Yii::error(isset($event->changedAttributes[$this->attribute]));
		// \Yii::error('Model attribute: '.$this->status);
		if (isset($event->changedAttributes[$this->attribute])) {
			$messages = TelegramMessage::find()
				->where(['order_id' => $owner->id])
				->andWhere(['status' => TelegramMessage::STATUS_OPENED])
				->all();

			foreach ($messages as $message) {
				$message->hide();
			}
			if ($owner->status < Order::STATUS_DELIVERY && $owner->status != Order::STATUS_PREPARE) {
				$employees = Employee::find()
					->where(['state_id' => $event->changedAttributes[$this->attribute]])
					->all();
				if (count($employees)) {
					foreach ($employees as $employee) TelegramMessage::send($employee, $owner);
				}
			}
		}
	}
}