<?php

namespace backend\controllers;

use common\models\Client;
use Yii;
use yii\data\ActiveDataProvider;

class ClientController extends BaseController
{
	public function actionIndex() {
		return $this->render('index', [
			"clientProvider" => new ActiveDataProvider([
				"query" => Client::find()
			])
		]);
	}

	public function actionUpdate($id)
	{
		$model = Client::findOne($id);
		if ( Yii::$app->request->isPost ) {
			if ( $model->load(Yii::$app->request->post()) && $model->save() ) {
				Yii::$app->session->setFlash("success", Yii::t("app", "Client info is saved"));
				return $this->redirect("client/index");
			}
			Yii::$app->session->setFlash("error", Yii::t("app", "Failed! Client info is not saved!"));
			Yii::error($model->getErrorSummary(true));
		}
		return $this->render("create", [
			"model" => $model
		]);
	}

	public function actionCreate()
	{
		$model = new Client();
		if ( Yii::$app->request->isPost ) {
			if ( $model->load(Yii::$app->request->post()) && $model->save() ) {
				Yii::$app->session->setFlash("success", Yii::t("app", "Client info is saved"));
				return $this->redirect(["client/index"]);
			}
			Yii::$app->session->setFlash("error", Yii::t("app", "Failed! Client info is not saved!"));
			Yii::error($model->getErrorSummary(true));
		}
		return $this->render("create", [
			"model" => $model
		]);
	}

	public function actionView($id)
	{
		$model = Client::findOne($id);
		return $this->render("view", [
			"model" => $model
		]);
	}

	public function actionDelete($id)
	{
		$model = Client::findOne($id);
		$model->delete();
		return $this->redirect("index");
	}

	public function actionInvite($id)
	{

	}
}