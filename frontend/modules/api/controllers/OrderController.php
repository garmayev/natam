<?php
namespace frontend\modules\api\controllers;

use common\models\Order;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

class OrderController extends ActiveController {
    public $modelClass = Order::class;

    public function behaviors()
    {
	$behaviors = parent::behaviors();
	$behaviors['authenticator'] = [
	    'class' => CompositeAuth::class,
	    'authMethods' => [
		HttpBasicAuth::class,
		HttpBearerAuth::class,
		QueryParamAuth::class,
	    ],
	    'except' => ['options'],
	];
	$behaviors['corsFilter'] = [
	    'class' => \yii\filters\Cors::class,
	];
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
	\Yii::error(\Yii::$app->user->id);
	if (\Yii::$app->user->can("employee")) {
	    return $this->modelClass::find()->where(['<', 'status', Order::STATUS_COMPLETE])->all();
	} else {
	    return $this->modelClass::find()->where(['client_id' => \Yii::$app->user->identity->client->id])->all();
	}
    }
}