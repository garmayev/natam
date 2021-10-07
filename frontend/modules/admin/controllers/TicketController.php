<?php

namespace frontend\modules\admin\controllers;

use frontend\models\Ticket;
use yii\data\ActiveDataProvider;

class TicketController extends BaseController
{
	public function beforeAction($action)
	{
		$this->view->title = \Yii::t("app", "Tickets");
		return parent::beforeAction($action);
	}

	public function actionIndex()
	{
		return $this->render("index", [
			"ticketProvider" => new ActiveDataProvider([
				"query" => Ticket::find()
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

	public function actionConvert($client_id, $id)
	{
		\Yii::$app->session->set("isConvert", true);
		\Yii::$app->session->set("client_id", $client_id);
		\Yii::$app->session->set("ticket_id", $id);
		return $this->redirect(["order/create"]);
	}
}