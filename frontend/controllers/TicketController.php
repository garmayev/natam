<?php

namespace frontend\controllers;

use common\models\Client;
use common\models\Ticket;

class TicketController extends \yii\web\Controller
{
	public function actionCreate()
	{
		$ticket = new Ticket();
		$post = \Yii::$app->request->post();
		if ( \Yii::$app->request->isPost )
		{
			$phone = preg_replace("/[\(\)\ \+]*/", "", $post["Client"]["phone"], -1);
			$client = Client::find()->where(["phone" => $phone])->one();
			\Yii::error($client);
			if ( empty($client) )
			{
				$client = new Client();
				if ( !$client->load($post) || !$client->save() )
				{
					\Yii::$app->session->setFlash("error", \Yii::t("app", "Your client account is not created! Our programmer is worked in"));
					\Yii::error($client->getErrorSummary(true));
				}
			}
			$ticket->client_id = $client->id;
			$ticket->service_id = $post["Ticket"]["service_id"];
			if ( $ticket->save() )
			{
				\Yii::$app->session->setFlash("success", \Yii::t("app", "Your ticket is successfully created! Manager was calling you"));
			} else {
				\Yii::$app->session->setFlash("error", \Yii::t("app", "Your ticket is not created! Our programmer is worked in"));
				\Yii::error($ticket->getErrorSummary(true));
			}
		}
		return $this->redirect("/");
	}
}