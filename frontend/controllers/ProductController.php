<?php

namespace frontend\controllers;

use common\models\Product;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\Response;

class ProductController extends Controller
{
	public function actionIndex()
	{
		$this->view->title = "Технические газы";
		return $this->render("index", [
			"productProvider" => new ActiveDataProvider([
				"query" => Product::find()
			])
		]);
	}

	public function actionGetProduct($id)
	{
		$model = Product::findOne($id);
		\Yii::$app->response->format = Response::FORMAT_JSON;
		return $model;
	}
}