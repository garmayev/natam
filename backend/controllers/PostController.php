<?php

namespace backend\controllers;

use common\models\Post;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;

class PostController extends BaseController
{
	public function beforeAction($action)
	{
		$this->view->title = Yii::t("app", "News");
		return parent::beforeAction($action);
	}

	public function actionIndex()
	{
		return $this->render("index", [
			"postProvider" => new ActiveDataProvider([
				"query" => Post::find()
			])
		]);
	}

	public function actionView($id)
	{
		$news = Post::findOne($id);
		return $this->render("view", [
			"model" => $news
		]);
	}

	public function actionCreate()
	{
		$model = new Post();
		if ( Yii::$app->request->isPost )
		{
			$model->file = UploadedFile::getInstance($model, "file");
			if ( $model->load(Yii::$app->request->post()) && $model->upload() && $model->save() )
			{
				Yii::$app->session->setFlash("success", Yii::t("app", "News is successfully saved"));
				return $this->redirect("/admin/post/index");
			} else {
				Yii::$app->session->setFlash("error", Yii::t("app", "Failed! News is not saved! Our programmers already repair it!"));
				Yii::error($model->getErrorSummary(true));
			}
		}
		return $this->render("create", [
			"model" => $model
		]);
	}

	public function actionUpdate($id)
	{
		$model = Post::findOne($id);
		if ( Yii::$app->request->isPost )
		{
			$model->file = UploadedFile::getInstance($model, "file");
			if ( $model->load(Yii::$app->request->post()) && $model->upload() && $model->save() )
			{
				Yii::$app->session->setFlash("success", Yii::t("app", "News is successfully saved"));
				return $this->redirect("/admin/post/index");
			} else {
				Yii::$app->session->setFlash("error", Yii::t("app", "Failed! News is not saved! Our programmers already repair it!"));
				Yii::error($model->getErrorSummary(true));
			}
		}
		return $this->render("create", [
			"model" => $model
		]);
	}

	public function actionDelete($id)
	{
		$model = Post::findOne($id);
		unlink(Yii::getAlias("@webroot").$model->thumbs);
		$model->delete();
		return $this->redirect(["/admin/post/index"]);
	}
}