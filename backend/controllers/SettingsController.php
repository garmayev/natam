<?php

namespace backend\controllers;

use backend\models\Settings;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;

class SettingsController extends Controller
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
				'denyCallback' => function () {
					Url::remember(Url::current());
					return $this->redirect(['user/security/login']);
				}
			],
		];
	}

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