<?php

namespace frontend\modules\api\controllers;

use Yii;
use yii\base\Controller;
use yii\web\Response;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use \dektrium\user\models\LoginForm;

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
	    'except' => ['options', 'login'],
	];
	return $behaviors;
    }

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    public function actionOption()
    {
    }

    public function actionOptions($chat_id = null)
    {
	if ( $_GET['chat_id'] ) {
	    $employee = \common\models\staff\Employee::findOne(["chat_id" => $_GET['chat_id']]);
	    if ( empty($employee) ) {
		$client = \common\models\Client::findOne(["chat_id" => $_GET['chat_id']]);
		if ( !$client ) {
		    return ["ok" => false, "role" => "guest", "param" => Yii::$app->getRequest()->csrfParam, "token" => Yii::$app->getRequest()->getCsrfToken()];
		} else {
		    return ["ok" => true, "access_token" => $client->user->token->code, "role" => "person", "param" => Yii::$app->getRequest()->csrfParam, "token" => Yii::$app->getRequest()->getCsrfToken()];
		}
	    } else {
		return ["ok" => true, "access_token" => $employee->user->token->code, "role" => "employee", "param" => Yii::$app->getRequest()->csrfParam, "token" => Yii::$app->getRequest()->getCsrfToken()];
	    }
	} else {
	    return ["ok" => false, "role" => "guest", "param" => Yii::$app->getRequest()->csrfParam, "token" => Yii::$app->getRequest()->getCsrfToken()];
	}
    }

    public function actionLogin()
    {
	$loginForm = \Yii::createObject(LoginForm::className());
	if ( \Yii::$app->request->isPost ) {
	    $data = \Yii::$app->request->post();
	    if ( $loginForm->load(\Yii::$app->request->post()) && $loginForm->login() ) {
		$user = Yii::$app->user->identity;
		\Yii::error($user->attributes);
		if ( Yii::$app->user->can("employee") ) {
		    $employee = $user->employee;
		    $employee->chat_id = $data["chat_id"];
		    $employee->save();
		    return ["ok" => true, "access_token" => $user->token->code, "role" => "employee", "param" => Yii::$app->getRequest()->csrfParam, "token" => Yii::$app->getRequest()->getCsrfToken()];
		} else {
		    $client = $user->client;
		    $client->chat_id = $data["chat_id"];
		    $client->save();
		    return ["ok" => true, "access_token" => $user->token->code, "role" => "person", "param" => Yii::$app->getRequest()->csrfParam, "token" => Yii::$app->getRequest()->getCsrfToken()];
		}
	    }
	    return ["ok" => false, "message" => $loginForm->getErrorSummary(true)];
	}
	return ["ok" => false, "message" => "Wrong request"];
    }
}