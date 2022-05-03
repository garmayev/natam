<?php

namespace backend\controllers;

use common\models\Category;
use common\models\Product;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;

class ProductController extends BaseController
{
	public function beforeAction($action)
	{
		$this->view->title = Yii::t("app", "Product");
		return parent::beforeAction($action);
	}

	public function actionIndex()
	{
		return $this->render("index", [
			"categoryProvider" => new ActiveDataProvider([
				"query" => Category::find()
			]),
			'productProvider' => new ActiveDataProvider([
				"query" => Product::find()
			]),
		]);
	}

	public function actionView($id)
	{
		$model = Product::findOne($id);
		return $this->render("view", [
			"model" => $model
		]);
	}

	public function actionUpdate($id)
	{
		$model = Product::findOne($id);
		if ( Yii::$app->request->isPost )
		{
			$model->file = UploadedFile::getInstance($model, "file");
			if ( $model->load(Yii::$app->request->post()) && $model->upload() && $model->save() ) {
				return $this->redirect(["category/view", "id" => $model->category_id]);
			} else {
				Yii::error($model->getErrorSummary(true));
			}
		}
		return $this->render("create", [
			"model" => $model
		]);
	}

	public function actionCreate()
	{
		$model = new Product();
		if ( Yii::$app->request->isPost )
		{
			$model->file = UploadedFile::getInstance($model, "file");
			if ( $model->load(Yii::$app->request->post()) && $model->upload() && $model->save() ) {
				return $this->redirect(["category/view", "id" => $model->category_id]);
			} else {
				Yii::error($model->getErrorSummary(true));
			}
		}
		return $this->render("create", [
			"model" => $model
		]);
	}

	public function actionDelete($id)
	{
		$model = Product::findOne($id);
		$model->delete();
		return $this->redirect(["category/view", "id" => $model->category_id]);
	}
}