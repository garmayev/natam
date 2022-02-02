<?php

namespace console\controllers;

use common\models\Order;
use console\models\Alert;
use frontend\models\Telegram;
use frontend\models\Updates;
use frontend\modules\admin\models\Settings;
use garmayev\staff\models\Employee;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\httpclient\Client;

class NotifyController extends \yii\console\Controller
{
	private $settings;
	public function init()
	{
		$settings = Settings::findOne(["name" => "notify"]);
		\Yii::$app->params["notify"] = $settings->getContent()["notify"];
		$this->settings = $settings->getContent()["notify"];
		parent::init();
	}

	public function actionIndex()
	{
		$models = Order::find()->where(["<=", "status", Order::STATUS_COMPLETE])->all();
		foreach ($models as $model) {
			$this->stdout("Заказ #{$model->id}\n", Console::BOLD);
			if ( $this->isNeedNextMessage($model) ) {
				$this->stdout("\tТребуется отправка сообщения сотруднику\n");
				$employee = $this->findNextEmployee($model);
				if ( $employee ) {
					$this->stdout("\tДля уведомления был выбран сотрудник {$employee->family} {$employee->name}\n");
					$this->sendMessage($employee, $model);
				} else {
					$this->stdout("\tНе найден подходящий сотрудник для уведомления\n");
				}
			}
			if ( $this->isNeedAlert($model) ) {
				$this->stdout("\tТребуется отправка сообщения начальнику\n");
				$model->boss_chat_id = $this->settings["alert"][$model->status - 1]["chat_id"];
				$model->save();
				Telegram::sendMessage([
					"chat_id" => $this->settings["alert"][$model->status - 1]["chat_id"],
					"text" => "Заказ #{$model->id}, находящийся в статусе {$model->getStatus($model->status)} никто не обработал"
				]);
			}
		}
	}

	/**
	 * @param $model
	 * @return Employee|null
	 */
	protected function findNextEmployee($model)
	{
		$usedEmployees = [];
		$updates = Updates::find()->where(["order_id" => $model->id])->andWhere(["order_status" => $model->status])->all();
		foreach ($updates as $update) {
			$usedEmployees[] = $update->employee->id;
		}
		return Employee::find()->where(["not in", "id", $usedEmployees])->andWhere(["state_id" => $model->status])->orderBy(["last_message_at" => SORT_ASC])->one();
	}

	/**
	 * @param Order $model
	 * @return int
	 */
	protected function isNeedNextMessage($model)
	{
		$update = Updates::find()->where(["order_id" => $model->id])->andWhere(["order_status" => $model->status])->orderBy(["created_at" => SORT_DESC])->one();
		if ( $update ) {
			if (time() < $update->created_at + $this->settings["limit"][$model->status - 1] ) {
				return 0;
			} else {
				return 1;
			}
		}
		return 2;
	}

	/**
	 * @param Order $model
	 * @return bool
	 */
	protected function isNeedAlert($model)
	{
		$timeout = ( time() - ($model->delivery_date - $this->settings["alert"][$model->status - 1]["time"]) > 0 );
		return ( $timeout && empty($model->boss_chat_id) );
	}

	/**
	 * @param $employee
	 * @param Order $model
	 * @return bool
	 */
	protected function sendMessage($employee, $model)
	{
		$response = Telegram::sendMessage(["chat_id" => $employee->chat_id, "text" => $model->generateTelegramText(), "reply_markup" => json_encode(["inline_keyboard" => $model->generateTelegramKeyboard()])]);
		if ( !$response->isOk ) {
			\Yii::error($response->getContent());
			return false;
		}
		$data = $response->getData();
		$update = new Updates([
			"order_id" => $model->id,
			"order_status" => $model->status,
			"employee_id" => $employee->id,
			"message_id" => $data["result"]["message_id"],
			"message_timestamp" => $data["result"]["date"],
		]);
		$update->save();
		return true;
	}
}
