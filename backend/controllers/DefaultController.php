<?php

namespace backend\controllers;

use common\models\Client;
use common\models\Order;
use common\models\staff\Employee;
use Da\QrCode\QrCode;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Response;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends BaseController
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
				'denyCallback' => function () {
					Url::remember(Url::current());
					return $this->redirect(['user/security/login']);
				}
			],
		];
	}

	/**
     * Renders the index view for the module
     * @return string
     */
	/**
	 * Displays homepage.
	 *
	 * @return string
	 */
	public function actionIndex()
	{
		$cars = $this->cars();
		return $this->render('index', [
			"cars" => $cars
		]);
	}

    public function actionMap()
    {
        return $this->render('map');
    }

	public function cars()
	{
		$units = [];
		$token = \Yii::$app->runAction('cars/login')["SessionId"];
		$ids = \Yii::$app->runAction('cars/units', ['token' => "$token"]);
        if ( $ids ) {
            foreach ($ids["Units"] as $id) {
                $units[$id["UnitId"]] = [
                    "name" => $id["Name"],
                    "driver" => Employee::findOne(["car" => $id["UnitId"]])
                ];
            }
        }
		\Yii::$app->response->format = Response::FORMAT_HTML;
		return $units;
	}
}
