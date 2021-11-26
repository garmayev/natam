<?php

namespace frontend\modules\admin\controllers;

use common\models\Vacancy;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;

class VacancyController extends BaseController
{
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