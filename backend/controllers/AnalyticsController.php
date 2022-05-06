<?php

namespace backend\controllers;

use common\models\Order;
use garmayev\staff\models\Employee;
use kartik\mpdf\Pdf;

class AnalyticsController extends BaseController
{
	public function actionIndex()
	{
		$models = Order::find()->all();
		return $this->render('employee', [
			"models" => $models
		]);
	}

	public function actionMonth()
	{
		$models = Order::find()->all();
		return $this->render("month");
	}

	public function actionOrders($from_date = null, $to_date = null)
	{
		$models = Order::find();
		if ( !is_null($from_date) ) {
			$models->andWhere(['>', 'created_at', \Yii::$app->formatter->asTimestamp($from_date)]);
		}
		if ( !is_null($to_date) ) {
			$models->andWhere(['<', 'created_at', \Yii::$app->formatter->asTimestamp($to_date)]);
		}
		return $this->render("orders", [
			"models" => $models->all()
		]);
	}

	public function actionEmployee($startDate = null, $finishDate = null, $export = false)
	{
		$employees = Employee::find()->all();
		return $this->render('employee', [
			'models' => $employees
		]);
	}

	public function actionExportEmployee($startDate, $finishDate)
	{

	}
}