<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;

class BaseController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => \Yii::$app->user->can("person"),
						'roles' => ['@'],
					],
				],
				'denyCallback' => function () {
					if ( \Yii::$app->user->isGuest ) {
						return $this->redirect(["user/login"]);
					} else {
						\Yii::$app->session->setFlash("error", \Yii::t("app", "You don`t have any permission to access this section!"));
						return $this->redirect(["/"]);
					}
				}
			],
		];
	}
}