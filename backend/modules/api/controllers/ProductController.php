<?php

namespace backend\modules\api\controllers;

use common\models\Product;
use yii\data\ActiveDataProvider;

class ProductController extends \yii\rest\ActiveController
{
    public $modelClass = Product::class;

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
        $query = Product::find();
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
    }
}