<?php

namespace backend\controllers;

use common\models\Order;

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

	public function actionOrders()
	{
		$models = Order::find()->all();
		return $this->render("orders", [
			"models" => $models
		]);
	}

	public function actionEmployee()
	{
		$models = Order::find()->all();
		return $this->render("employee", [
			"models" => $models,
		]);
	}
}