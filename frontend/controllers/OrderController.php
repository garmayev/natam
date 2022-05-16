<?php

namespace frontend\controllers;

use common\models\Client;
use common\models\Location;
use common\models\Order;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsTrait;
use Yii;

class OrderController extends \yii\web\Controller
{
	use SaveRelationsTrait;

	public function actionCreate()
	{
		$this->enableCsrfValidation = false;
		$order = new Order();
		$post = Yii::$app->request->post();
		// Yii::error($post);
		if (Yii::$app->request->isPost) {
			if ( isset($post["OrderProduct"]) ) {
				$order->orderProduct = $_POST["OrderProduct"];
				$data = array_merge_recursive($post, ["Order" => ["orderProduct" => $_POST["OrderProduct"]]]);
			} else {
				$data = $post;
			}
			$order->delivery_date = Yii::$app->formatter->asTimestamp(Yii::$app->request->post()["Order"]["delivery_date"]);
			$order->loadRelations($data);
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