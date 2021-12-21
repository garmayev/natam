<?php

namespace console\controllers;

use common\models\Order;
use console\models\Alert;
use frontend\models\Updates;
use frontend\modules\admin\models\Settings;
use garmayev\staff\models\Employee;
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
		$models = Order::find()->all();
		foreach ($models as $model) {
			$this->stdout("Заказ #{$model->id}\n", Console::BOLD);
			if ( $this->isNeedNextMessage($model) ) {
				$this->stdout("\tТребуется отправка сообщения сотруднику\n");
			}
			if ( $this->isNeedAlert($model) ) {
				$this->stdout("\tТребуется отправка сообщения начальнику\n");
			}
		}
	}

	/**
	 * @param Order $model
	 * @return bool
	 */
	protected function isNeedNextMessage($model)
	{
		$update = Updates::find()->where(["order_id" => $model->id])->andWhere(["order_status" => $model->status])->orderBy(["created_at" => SORT_DESC])->one();
		if ( $update ) {
			return (time() + $this->settings["limit"][$model->status + 1] < $update->created_at);
		}
		return true;
	}

	/**
	 * @param Order $model
	 * @return bool
	 */
	protected function isNeedAlert($model)
	{
		return ( time() - ($model->delivery_date + $this->settings["alert"][$model->status - 1]["time"]) > 0 );
	}

	protected function findEmployee()
	{

	}
}
