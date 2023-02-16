<?php

namespace frontend\modules\api\controllers;

use Yii;
use yii\base\Controller;
use yii\web\Response;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

class DefaultController extends Controller
{
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
	    'except' => ['option'],
    ];
    return $behaviors;

    }
    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    public function actionOption($chat_id = null)
    {
	if ( $_GET['chat_id'] ) {
	    $employee = \common\models\staff\Employee::find(["chat_id" => $_GET['chat_id']])->one();
	    $data = $employee->attributes;
	    if ( empty($employee) ) {
		$client = \common\models\Client::find(["chat_id" => $_GET['chat_id']])->one();
		if ( !$client ) {
		    return [Yii::$app->getRequest()->csrfParam => Yii::$app->getRequest()->getCsrfToken()];
		} else {
		    return ["ok" => true, "access_token" => $client->user->token->code, "role" => "client", "param" => Yii::$app->getRequest()->csrfParam, "token" => Yii::$app->getRequest()->getCsrfToken()];
		}
	    } else {
		return ["ok" => true, "chat_id" => $_GET['chat_id'], "data" => $data, "access_token" => $employee->user->token->code, "role" => "employee", "param" => Yii::$app->getRequest()->csrfParam, "token" => Yii::$app->getRequest()->getCsrfToken()];
	    }
	} else {
	    return ["ok" => false, "chat_id" => $_GET['chat_id'], "param" => Yii::$app->getRequest()->csrfParam, "token" => Yii::$app->getRequest()->getCsrfToken()];
	}
    }

    public function actionLogin()
    {
	return ["ok" => true];
    }
}