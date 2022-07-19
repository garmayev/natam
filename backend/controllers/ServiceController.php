<?php

namespace backend\controllers;

use common\models\Service;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\UploadedFile;

class ServiceController extends BaseController
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
		$this->view->title = \Yii::t("app", "Services");
		return $this->render("index", [
			"serviceProvider" => new ActiveDataProvider([
				"query" => Service::find()
			])
		]);
	}

	public function actionView($id)
	{
		$model = Service::findOne($id);
		$this->view->title = Yii::t("app", $model->title);
		return $this->render("view", [
			"model" => $model
		]);
	}

	public function actionUpdate($id)
	{
		$model = Service::findOne($id);
		$this->view->title = Yii::t("app", "Update Service");
		if ( Yii::$app->request->isPost ) {
			$model->file = UploadedFile::getInstance($model, "file");
			if ( $model->load(Yii::$app->request->post()) && $model->upload() && $model->save() ) {
				return $this->redirect(["index"]);
			} else {
				Yii::$app->session->setFlash("error", Yii::t("app", "Failed! Service is not saved"));
				Yii::error($model->getErrorSummary(true));
			}
		}
		return $this->render('create', [
			"model" => $model
		]);
	}
}