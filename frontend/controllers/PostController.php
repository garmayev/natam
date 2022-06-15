<?php

namespace frontend\controllers;

use common\models\Post;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

class PostController extends Controller
{
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
		$model = Post::findOne($id);
		if (empty($model)) {
			throw new \yii\web\NotFoundHttpException();
		}
		return $this->render("view", [
			"model" => $model
		]);
	}
}