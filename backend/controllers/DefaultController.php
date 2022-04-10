<?php

namespace backend\controllers;

use common\models\Order;
use yii\filters\AccessControl;

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
        return $this->render('index');
    }
}
