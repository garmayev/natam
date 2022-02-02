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
		$order = new Order();
		$post = Yii::$app->request->post();
		Yii::error($post);
		if (Yii::$app->request->isPost) {
			$client = Client::findByPhone($post["Client"]["phone"]);
			if ( !isset($client) ) {
				$client = new Client();
				if (!$client->load($post) || !$client->save()) {
					Yii::error($client->getErrorSummary(true));
					Yii::$app->session->setFlash("error", Yii::t('app', 'Failed! Client info is not saved!'));
					return $this->redirect("/");
				}
			}
			$order->client_id = $client->id;
			$location = new Location();
			if (!$location->load($post) || !$location->save()) {
				Yii::error($location->getErrorSummary(true));
				Yii::$app->session->setFlash("error", Yii::t('app', 'Failed! Delivery info is not saved!'));
				return $this->redirect("/");
			}
			$order->location_id = $location->id;
			$order->delivery_date = Yii::$app->formatter->asTimestamp($post["Order"]["delivery_date"]);
			if ($order->load($post) && $order->save()) {
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