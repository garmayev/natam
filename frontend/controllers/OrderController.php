<?php

namespace frontend\controllers;

use common\models\Client;
use common\models\Location;
use common\models\Order;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsTrait;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\Response;

class OrderController extends \yii\web\Controller
{
	use SaveRelationsTrait;

	public function beforeAction($action)
	{
		Yii::$app->request->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}

	public function actionCreate()
	{
		$this->enableCsrfValidation = false;
		$order = new Order();
		$post = Yii::$app->request->post();

		if (Yii::$app->request->isPost) {
			$order->save(false);
			if ($order->load(Yii::$app->request->post()) && $order->save()) {
				Yii::$app->cart->clear();
				Yii::$app->session->setFlash("success", Yii::t("app", "Order was created! Manager was calling you"));
			} else {
				Yii::$app->session->setFlash("error", Yii::t("app", "Failed! Order was not created!"));
				Yii::error($order->getErrorSummary(true));
			}
		} else {
                        $order->loadDefaultValues();
                }
		return $this->redirect("/");
	}
}