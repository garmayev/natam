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
//			Yii::error(json_encode($client->name));
//			Yii::error($phone);
			if ( empty($client) ) {
				$client = new Client();
				if ( !$client->load($post) || !$client->save() ) {
					Yii::error($client->getErrorSummary(true));
					Yii::$app->session->setFlash("error", "Failed! ".json_encode($client->getErrorSummary(true)));
				}
			}
			$order->client_id = $client->id;
			if ( $order->load($post) && $order->save() ) {
				Yii::$app->session->setFlash("success", "Order was created! Manager was calling you");
				return $this->redirect("/");
			} else {
				Yii::$app->session->setFlash("error", "Failed! Order was not created!");
				Yii::error($order->getErrorSummary(true));
			}
		}
		return $this->redirect("/");
	}
}