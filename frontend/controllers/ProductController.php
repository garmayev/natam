<?php

namespace frontend\controllers;

use common\models\Product;
use common\models\Category;
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

	public function actionByCategory($category_id)
	{
		\Yii::$app->response->format = Response::FORMAT_JSON;
		$model = Category::findOne($category_id);
		$result = [];
		foreach ( $model->products as $product ) {
			$result[] = $product;
		}
		return $result;
	}
}