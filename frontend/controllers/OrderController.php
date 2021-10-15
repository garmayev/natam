<?php

namespace frontend\controllers;

use frontend\models\Client;
use frontend\models\Order;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsTrait;
use Yii;

class OrderController extends \yii\web\Controller
{
	use SaveRelationsTrait;

	public function actionCreate()
	{
		$order = new Order();
		$post = Yii::$app->request->post();
		if ( Yii::$app->request->isPost ) {
			$phone = preg_replace("/[\(\)\ \+]*/", "", $post["Client"]["phone"], -1);
			$client = Client::find()->where(["phone" => $phone])->one();
			if ( empty($client) ) {
				$client = new Client();
				if ( !$client->load($post) || !$client->save() ) {
					Yii::error($client->getErrorSummary(true));
					Yii::$app->session->setFlash("error", "Failed! Client info is not saved!");
				}
			}
			$order->client_id = $client->id;
			if ( $order->load($post) && $order->save() ) {
				Yii::$app->session->setFlash("success", Yii::t("app", "Order was created! Manager was calling you"));
				return $this->redirect("/");
			} else {
				Yii::$app->session->setFlash("error", Yii::t("app", "Failed! Order was not created!"));
				Yii::error($order->getErrorSummary(true));
			}
		}
		return $this->redirect("/");
	}
}