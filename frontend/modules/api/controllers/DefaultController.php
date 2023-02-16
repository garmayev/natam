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

    public function actionOption()
    {
        return [Yii::$app->getRequest()->csrfParam => Yii::$app->getRequest()->getCsrfToken()];
    }

    public function actionLogin()
    {

    }
}