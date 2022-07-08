<?php

namespace backend\controllers;

use common\models\Order;
use common\models\staff\Employee;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends BaseController
{
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

	public function cars()
	{
		$units = [];
		$token = \Yii::$app->runAction('cars/login')["SessionId"];
		$ids = \Yii::$app->runAction('cars/units', ['token' => "$token"]);
		foreach ( $ids["Units"] as $id ) {
			$units[$id["UnitId"]] = [
				"name" => $id["Name"],
				"driver" => Employee::findOne(["car" => $id["UnitId"]])
			];
		}
		\Yii::$app->response->format = Response::FORMAT_HTML;
		return $units;
	}

}
