<?php

namespace backend\controllers;

use common\models\Client;
use common\models\Ticket;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;

class TicketController extends BaseController
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
		$this->view->title = \Yii::t("app", "Tickets");
		return parent::beforeAction($action);
	}

	public function actionIndex()
	{
		if ( !\Yii::$app->user->can("employee") ) {
			$client = Client::findOne(['phone' => \Yii::$app->user->identity->username]);
			$query = Ticket::find()->where(["client_id" => $client->id]);
		} else {
			$query = Ticket::find();
		}
		return $this->render("index", [
			"ticketProvider" => new ActiveDataProvider([
				"query" => $query
			])
		]);
	}

	public function actionUpdate($id)
	{
		$model = Ticket::findOne($id);
		if ( \Yii::$app->request->isPost )
		{
			if ( $model->load(\Yii::$app->request->post()) && $model->save() )
			{
				return $this->redirect(["/admin/ticket/view", "id" => $id]);
			}
		}
		return $this->render("create", [
			"model" => $model
		]);
	}

	public function actionView($id)
	{
		$ticket = Ticket::findOne($id);
		return $this->render("view", [
			"model" => $ticket
		]);
	}

	public function actionDelete($id)
	{
		$model = Ticket::findOne($id);
		if ( $model ) {
			$model->delete();
		}
		return $this->redirect(["index"]);
	}

	public function actionConvert($client_id, $id)
	{
		\Yii::$app->session->set("isConvert", true);
		\Yii::$app->session->set("client_id", $client_id);
		\Yii::$app->session->set("ticket_id", $id);
		return $this->redirect(["order/create"]);
	}
}