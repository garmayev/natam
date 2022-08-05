<?php

namespace backend\modules\api\controllers;

use backend\modules\api\models\Order;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\web\Response;

class OrderController extends ActiveController
{
	public $modelClass = Order::class;

	public function actionIndex()
	{
		\Yii::$app->response->format = Response::FORMAT_RAW;
		\Yii::$app->response->headers->add('Content-Type', 'text/xml');
		return $this->render('index', [
			'dataProvider' => new ActiveDataProvider([
				'query' => Order::find()
			])
		]);
	}
}