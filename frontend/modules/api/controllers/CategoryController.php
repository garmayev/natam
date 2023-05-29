<?php

namespace frontend\modules\api\controllers;

use common\models\Category;
use yii\rest\ActiveController;

class CategoryController extends ActiveController
{
    public $modelClass = Category::class;

    public function behaviors()
    {
	$behaviors = parent::behaviors();

	// remove authentication filter
	$auth = $behaviors['authenticator'];
	unset($behaviors['authenticator']);

	// add CORS filter
	$behaviors['corsFilter'] = [
	    'class' => \yii\filters\Cors::class,
	];

	// re-add authentication filter
	$behaviors['authenticator'] = $auth;
	// avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
	$behaviors['authenticator']['except'] = ['options'];

	return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);

        return $actions;
    }

    public function actionIndex()
    {
        return $this->modelClass::find()->where(['main' => 1])->all();
    }

    public function actionSearch($id)
    {
	return $this->modelClass::findOne($id);
    }
}