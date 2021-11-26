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
			$location = new Location();
//			var_dump($post["Location"]); die;
			$location->title = $post["Location"]["title"];
			$location->latitude = $post["Location"]["latitude"];
			$location->longitude = $post["Location"]["longitude"];
			if ( $location->load($post) && $location->save() ) {
				$order->location_id = $location->id;
				$order->client_id = $client->id;
				$order->delivery_date = Yii::$app->formatter->asTimestamp($post["Order"]["delivery_date"]);
				if ($order->load($post) && $order->save()) {
					Yii::$app->session->setFlash("success", Yii::t("app", "Order was created! Manager was calling you"));
					return $this->redirect("/");
				} else {
					Yii::$app->session->setFlash("error", Yii::t("app", "Failed! Order was not created!"));
					Yii::error($order->getErrorSummary(true));
				}
			} else {
				Yii::error($location->getErrorSummary(true));
			}
		}
//		var_dump($order); die;
		return $this->redirect("/");
	}
}