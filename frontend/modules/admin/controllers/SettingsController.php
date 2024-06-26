<?php

namespace frontend\modules\admin\controllers;

use frontend\modules\admin\models\Settings;
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
//		var_dump($post); die;
		$setting = Settings::findOne(["name" => "notify"]);
		$setting->content = json_encode($post["Settings"]);
		$setting->save();
		return $this->redirect(["/admin/settings/index"]);
	}
}