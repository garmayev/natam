<?php

namespace frontend\controllers;

use frontend\models\Product;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

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
}