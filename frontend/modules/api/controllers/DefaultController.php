<?php

namespace frontend\modules\api\controllers;

use Yii;
use yii\base\Controller;
use yii\web\Response;

class DefaultController extends Controller
{
    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    public function actionOption($chat_id = null)
    {
	if ( $_GET['chat_id'] ) {
	    $employee = \common\models\staff\Employee::find(["chat_id" => $_GET['chat_id']])->one();
	    if ( !$employee ) {
		$client = \common\models\Client::find(["chat_id" => $_GET['chat_id']])->one();
		if ( !$client ) {
		    return [Yii::$app->getRequest()->csrfParam => Yii::$app->getRequest()->getCsrfToken()];
		} else {
		    return ["ok" => true, "user" => $client->user, "role" => "client", Yii::$app->getRequest()->csrfParam => Yii::$app->getRequest()->getCsrfToken()];
		}
	    } else {
		return ["ok" => true, "user" => $employee->user, "role" => "employee", Yii::$app->getRequest()->csrfParam => Yii::$app->getRequest()->getCsrfToken()];
	    }
	} else {
	    return ["ok" => false, "chat_id" => $chat_id, Yii::$app->getRequest()->csrfParam => Yii::$app->getRequest()->getCsrfToken()];
	}
    }

    public function actionLogin()
    {

    }
}