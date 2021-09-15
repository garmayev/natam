<?php

namespace frontend\modules\admin\controllers;

use frontend\models\Service;
use yii\data\ActiveDataProvider;

class ServiceController extends BaseController
{
	public function actionIndex()
	{
		$this->view->title = \Yii::t("app", "Services");
		return $this->render("index", [
			"serviceProvider" => new ActiveDataProvider([
				"query" => Service::find()
			])
		]);
	}
}