<?php

namespace frontend\modules\api\controllers;

use common\models\Client;
use yii\rest\ActiveController;

class ClientController extends ActiveController {
    public $modelClass = Client::class;

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

}