<?php

namespace frontend\controllers;

use frontend\models\Service;
use yii\data\ActiveDataProvider;

class ServiceController extends \yii\web\Controller
{
	public function actionIndex()
	{
		return $this->render("index", [
			"serviceProvider" => new ActiveDataProvider([
				"query" => Service::find()
			])
		]);
	}

	public function actionView($id)
	{
		$model = Service::findOne($id);
		return $this->render("view", [
			"model" => $model
		]);
	}
}