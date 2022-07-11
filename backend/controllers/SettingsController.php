<?php

namespace backend\controllers;

use backend\models\Settings;
use Yii;
use yii\web\Controller;

class SettingsController extends Controller
{
	public function actionIndex()
	{
		$model = Settings::find()->one();
		return $this->render("index", [
			"model" => $model
		]);
	}

	public function actionUpdate()
	{
		$post = Yii::$app->request->post();
		$setting = Settings::findOne(["name" => "notify"]);
		$setting->content = json_encode($post["Settings"]);
		$setting->save();
		return $this->redirect(["/settings/index"]);
	}
}