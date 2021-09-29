<?php

namespace frontend\modules\admin\controllers;

use frontend\models\Order;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends BaseController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', [
			'order' => Order::findOne(56)
        ]);
    }
}
