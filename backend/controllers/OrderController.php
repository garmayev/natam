<?php

namespace backend\controllers;

use common\models\Client;
use common\models\Location;
use common\models\Order;
use common\models\search\OrderSearch;
use common\models\Ticket;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Response;

class OrderController extends BaseController
{
	public function behaviors()
	{
		return array_merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => function () {
							return \Yii::$app->user->can("person");
						},
					]
				],
				'denyCallback' => function () {
					if ( \Yii::$app->user->isGuest ) {
						return $this->redirect(["user/login"]);
					} else {
						\Yii::$app->session->setFlash("error", \Yii::t("app", "You don`t have any permission to access this section!"));
						return $this->redirect(["/"]);
					}
				}
			]
		]);
	}

	public function beforeAction($action)
	{
		$this->view->title = Yii::t("app", "Order");
		return parent::beforeAction($action);
	}

	public function actionIndex()
	{
		if ( Yii::$app->user->can("employee") ) {
			$query = Order::find()->where(["<", "status", Order::STATUS_COMPLETE]);
		} else {
			$query = Order::find()->where(["client_id" => Yii::$app->user->identity->client->id])->andWhere(["<", "status", Order::STATUS_COMPLETE]);
		}
		return $this->render("index", [
			"orderProvider" => new ActiveDataProvider([
				"query" => $query
			]),
//			"searchModel" => $order,
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
		$post = Yii::$app->request->post();
		if (Yii::$app->session->get("isConvert")) {
			$order->client_id = Yii::$app->session->get("client_id");
			$client = Client::findOne(["id" => $order->client_id]);
			$order->status = Order::STATUS_PREPARE;
			(Ticket::findOne(Yii::$app->session->get("ticket_id")))->delete();
			Yii::$app->session->remove("isConvert");
			Yii::$app->session->remove("client_id");
			Yii::$app->session->remove("ticket_id");
		}
		if ( Yii::$app->request->isPost ) {
				if ($order->load($post) && $order->save()) {
					Yii::$app->session->setFlash("success", Yii::t("app", "Order was created! Manager was calling you"));
					return $this->redirect("/");
				} else {
					Yii::$app->session->setFlash("error", Yii::t("app", "Failed! Order was not created!"));
					Yii::error($order->getErrorSummary(true));
				}
		}
		$client = new Client();
		return $this->render("create", [
			"model" => $order,
			"client" => $client
		]);
	}

	public function actionUpdate($id)
	{
		$client = null;
		$order = Order::findOne($id);
		if (Yii::$app->request->isPost) {

			$order->delivery_date = Yii::$app->formatter->asTimestamp(Yii::$app->request->post()["Order"]["delivery_date"]);
			$order->loadRelations(Yii::$app->request->post());
			if ($order->load(Yii::$app->request->post()) && $order->save()) {
				Yii::$app->session->setFlash("success", "Order information successfully updated!");
				return $this->redirect(["order/view", "id" => $id]);
			} else {
				Yii::$app->session->setFlash("error", "Failed! Order information is not updated!");
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
		if (Yii::$app->request->isAjax) {
			$order = Order::findOne($id);
			if ($order) {
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
		if (Yii::$app->request->isAjax) {
			$order = Order::findOne($id);
			if ($order) {
				$order->status = $status;
				if ($order->save()) {
					return ["ok" => true];
				}
				return ["ok" => false, "description" => "Order #{$id} is not saved"];
			}
			return ["ok" => false, "description" => "Order #{$id} is not found"];
		}
	}

	public function actionGetList()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$orders = Order::find()->where(["<", "status", Order::STATUS_HOLD])->all();
		$locations = [];
		foreach ($orders as $order) {
			$cart = [];
			foreach ($order->products as $product) {
				$cart[] = [
					"product" => $product,
					"count" => $order->getCount($product->id)
				];
			}
			$locations[] = ["id" => $order->id, "location" => $order->location, "order" => $order, "client" => $order->client, "cart" => $cart, "cost" => $order->getPrice()];
		}
		return $locations;
	}
}