<?php

namespace backend\controllers;

use common\models\Order;
use yii\filters\AccessControl;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends BaseController
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@', 'person'],
						'denyCallback' => function ($rule, $action) {
//							return date('d-m') === '31-10';
							return $this->redirect(["user/login"]);
						}
					],
				],
			],
		];
	}

	/**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
