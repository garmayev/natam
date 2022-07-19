<?php

namespace frontend\controllers;

use common\models\Client;
use common\models\Location;
use common\models\Order;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsTrait;
use Yii;
use yii\base\InvalidArgumentException;

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
		// Yii::error(json_encode($post));

		if (Yii::$app->request->isPost) {
			if ( isset($post["OrderProduct"]) ) {
				$order->orderProduct = $_POST["OrderProduct"];
				$data = array_merge_recursive($post, ["Order" => ["orderProduct" => $_POST["OrderProduct"]]]);
			} else {
				$data = $post;
			}
			try {
				$order->delivery_date = Yii::$app->formatter->asTimestamp(Yii::$app->request->post()["Order"]["delivery_date"]);
			} catch (InvalidArgumentException $e) {
				$order->delivery_date = Yii::$app->formatter->asTimestamp(Yii::$app->request->post()["Order"]["delivery_date"]." 9:00");
			}
//			$order->loadRelations($data);
			if ($order->load($data) && $order->save()) {
				Yii::$app->cart->clear();
				Yii::$app->session->setFlash("success", Yii::t("app", "Order was created! Manager was calling you"));
			} else {
				Yii::$app->session->setFlash("error", Yii::t("app", "Failed! Order was not created!"));
				Yii::error($order->getErrorSummary(true));
			}
		}
		return $this->redirect("/");
	}
}