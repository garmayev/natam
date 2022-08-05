<?php

namespace frontend\controllers;

use common\models\Service;
use yii\data\ActiveDataProvider;

class ServiceController extends \yii\web\Controller
{
	public function actionIndex()
	{
		$this->view->title = "Наши услуги";
		return $this->render("index", [
			"serviceProvider" => new ActiveDataProvider([
				"query" => Service::find()
			])
		]);
	}

	public function actionView($id)
	{
		$model = Service::findOne($id);
		$this->view->title = $model->title;
		return $this->render("view", [
			"model" => $model
		]);
	}
}