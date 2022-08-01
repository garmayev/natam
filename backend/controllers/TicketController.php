<?php

namespace backend\controllers;

use common\models\Client;
use common\models\Ticket;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Url;

class TicketController extends BaseController
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
				'denyCallback' => function () {
					Url::remember(Url::current());
					return $this->redirect(['user/security/login']);
				}
			],
		];
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