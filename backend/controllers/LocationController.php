<?php

namespace backend\controllers;

use common\models\Location;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;

class LocationController extends \yii\web\Controller
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

	public function beforeAction($action)
	{
		$this->view->title = Yii::t("app", "Address");
		return parent::beforeAction($action);
	}

	public function actionView($id)
	{
		$model = Location::findOne($id);
		return $this->render('view', [
			"model" => $model
		]);
	}
}