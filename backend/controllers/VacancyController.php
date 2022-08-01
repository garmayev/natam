<?php

namespace backend\controllers;

use frontend\models\Vacancy;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\UploadedFile;

class VacancyController extends BaseController
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
		$this->view->title = Yii::t("app", "Vacancy");
		return parent::beforeAction($action);
	}

	public function actionIndex()
	{
		return $this->render("index", [
			"vacancyProvider" => new ActiveDataProvider([
				"query" => Vacancy::find()
			])
		]);
	}

	public function actionCreate()
	{
		$model = new Vacancy();
		if ( Yii::$app->request->isPost ) {
			$model->file = UploadedFile::getInstance($model, "file");
			if ( $model->load(Yii::$app->request->post()) && $model->upload() && $model->save() ) {
				return $this->redirect(["/admin/vacancy/index"]);
			}
		}
		return $this->render("create", [
			"model" => $model
		]);
	}

	public function actionUpdate($id)
	{
		$model = Vacancy::findOne($id);
		if ( Yii::$app->request->isPost ) {
			$model->file = UploadedFile::getInstance($model, "file");
			if ( $model->load(Yii::$app->request->post()) && $model->upload() && $model->save() ) {
				return $this->redirect("/admin/vacancy/index");
			}
		}
		return $this->render("create", [
			"model" => $model
		]);
	}

	public function actionDelete($id)
	{
		$model = Vacancy::findOne($id);
		$model->delete();
		return $this->redirect("/admin/vacancy/index");
	}
}