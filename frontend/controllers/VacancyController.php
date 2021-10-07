<?php

namespace frontend\controllers;

use frontend\models\Vacancy;
use yii\data\ActiveDataProvider;

class VacancyController extends \yii\web\Controller
{
	public function actionIndex()
	{
		$this->view->title = \Yii::t("app", "Vacancy");
		return $this->render("index", [
			"vacancyProvider" => new ActiveDataProvider([
				"query" => Vacancy::find()
			])
		]);
	}
}