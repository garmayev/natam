<?php

namespace frontend\modules\admin\controllers;

use frontend\models\Client;
use frontend\models\Order;
use frontend\models\Ticket;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Response;

class OrderController extends BaseController
{
	public function beforeAction($action)
	{
		$this->view->title = Yii::t("app", "Order");
		return parent::beforeAction($action);
	}

	public function actionIndex()
	{
		return $this->render("index", [
			"orderProvider" => new ActiveDataProvider([
				"query" => Order::find()->where(["<", "status", Order::STATUS_COMPLETE]),
				"sort" => [
					"attributes" => [
						"id",
						"address",
						"comment",
						"status",
					]
				]
			])
		]);
	}

	public function actionView($id)
	{
		$model = Order::findOne($id);
		return $this->render("view", [
			"model" => $model
		]);
	}

	public function actionCreate()
	{
		$order = new Order();
		$client_id = Yii::$app->session->get("client_id");
		$client = null;
		$post = Yii::$app->request->post();
		if ( $client_id ) {
			$client = Client::findOne($client_id);
			Yii::$app->session->remove("client_id");
		}
		if ( Yii::$app->request->isPost ) {
			$client = Client::findOne(["phone" => $post["Client"]["phone"]]);
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
				$ticket = Ticket::findOne(Yii::$app->session->get("convert"));
				if ( $ticket ) {
					$ticket->delete();
					Yii::$app->session->remove("convert");
				}
				return $this->redirect("/admin/order/index");
			} else {
				Yii::$app->session->setFlash("error", "Failed! Order was not created!");
				Yii::error($order->getErrorSummary(true));
			}
		}
		return $this->render("create", [
			"model" => $order,
			"client" => $client
		]);
	}

	public function actionUpdate($id)
	{
		$client = null;
		$order = Order::findOne($id);
		if ( $client_id = Yii::$app->session->get("client_id") ) {
			$client = Client::findOne($client_id);
			Yii::$app->session->remove("client_id");
		}
		if ( Yii::$app->request->isPost )
		{
			if ( $order->load(Yii::$app->request->post()) && $order->save() )
			{
				Yii::$app->session->setFlash("success", "Order information successfully updated!");
				return $this->redirect(["/admin/order/view", "id" => $id]);
			} else {
				Yii::$app->session->setFlash("success", "Failed! Order information is not updated!");
				Yii::error($order->getErrorSummary(true));
			}
		}
		return $this->render("create", [
			"model" => $order,
			"client" => $client
		]);
	}

	public function actionDelete($id)
	{
		$model = Order::findOne($id);
		if ($model->delete()) {
			Yii::$app->session->setFlash("success", Yii::t("app", "Order #{n} is successfully deleted!", ["n" => $id]));
		} else {
			Yii::$app->session->setFlash("error", Yii::t("app", "Failed! Order #{n} is not deleted!", ["n" => $id]));
		}
		return $this->redirect("/admin/order/index");
	}

	public function actionUpdateComment($comment, $id)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		if ( Yii::$app->request->isAjax ) {
			$order = Order::findOne($id);
			if ( $order ) {
				$order->comment = $comment;
				if ($order->save()) {
					return ["ok" => true];
				}
				return ["ok" => false, "description" => "Order #{$id} is not saved"];
			}
			return ["ok" => false, "description" => "Order #{$id} is not found"];
		}
	}

	public function actionUpdateStatus($status, $id)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		if ( Yii::$app->request->isAjax ) {
			$order = Order::findOne($id);
			if ( $order ) {
				$order->status = $status;
				if ($order->save()) {
					return ["ok" => true];
				}
				return ["ok" => false, "description" => "Order #{$id} is not saved"];
			}
			return ["ok" => false, "description" => "Order #{$id} is not found"];
		}
	}
}