<?php

namespace backend\controllers;

use common\models\Client;
use common\models\search\ClientSearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class ClientController extends BaseController
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

	public function actionIndex() {
		$searchModel = new ClientSearch();
		$dataProvider = $searchModel->search($this->request->queryParams);
		return $this->render('index', [
			"clientProvider" => $dataProvider,
			"searchModel" => $searchModel
		]);
	}

	public function actionUpdate($id)
	{
		$model = Client::findOne($id);
		if ( Yii::$app->request->isAjax ) {
			$this->enableCsrfValidation = false;
			Yii::$app->response->format = Response::FORMAT_JSON;
			if ( $model->load(Yii::$app->request->post()) && $model->save() ) {
				return ["ok" => true];
			}
//			Yii::error($model->getErrorSummary(true));
			return ["ok" => false, "errors" => $model->getErrorSummary(true)];
		}
		if ( Yii::$app->request->isPost ) {
			if ( $model->load(Yii::$app->request->post()) && $model->save() ) {
				Yii::$app->session->setFlash("success", Yii::t("app", "Client info is saved"));
				return $this->redirect(Url::to(["client/view", "id" => $model->id]));
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
				return $this->redirect(Url::to(["client/view", "id" => $model->id]));
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
		if ( !Yii::$app->user->can('employee') ) {
			$responder = Client::findOne(["user_id" => Yii::$app->user->id]);
			if ( $responder->organization->boss_id === $responder->id || $model->id === $responder->id ) {
				return $this->render("view", [
					"model" => $model
				]);
			} else {
				throw new ForbiddenHttpException(Yii::t('app', 'Sorry, You don`t have permission to this command'));
			}
		}
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
		$smsRequest = $this->sendSms($id);
		if ( $smsRequest->isOk ) {
			echo $smsRequest->getContent();
		} else {
			Yii::error($smsRequest);
		}
	}

	private function shortLink($id)
	{
		$model = Client::findOne($id);
		$httpClient = (new \yii\httpclient\Client())->createRequest()
			->setUrl("https://clck.ru/--")
			->setData(["url" => "https://t.me/natam_trade_bot?start={$model->phone}"])
			->send();
		if ( $httpClient->isOk ) {
			return $httpClient->getContent();
		} else {
			return false;
		}
	}

	private function sendSms($id)
	{
		/**
		 * http://api.prostor-sms.ru/messages/v2/send/?phone=%2B71234567890&text=test
		 */
		$model = Client::findOne($id);
		$link = $this->shortLink($id);
		$text = "Для вашего удобства мы разработали Telegram bot {$link}";
		return (new \yii\httpclient\Client())->createRequest()
			->setUrl("http://api.prostor-sms.ru/messages/v2/send/")
			->setData([
				"login" => "ak141747",
				"password" => "914042",
				"phone" => $model->phone,
				"text" => $text
			])
			->send();
	}
}