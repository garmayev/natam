<?php

namespace backend\modules\api\controllers;

use backend\modules\api\models\Order;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\web\Response;

class OrderController extends ActiveController
{
	public $modelClass = Order::class;

    public function actions()
    {
        return [
            'index' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'prepareDataProvider' => [$this, 'getAllData']
            ],
        ];
    }

    public function getAllData()
    {
//        $modelClass = $this->modelClass;

//        $timestamp = $_GET["timestamp"];
        $query = (isset($_GET["timestamp"]) && !is_null($_GET["timestamp"])) ? Order::find()->where([">=", "created_at", $_GET["timestamp"]]) : Order::find();
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
    }
}