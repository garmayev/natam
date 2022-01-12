<?php

namespace frontend\modules\admin\controllers;

use yii\httpclient\Client;

class SpikController extends \yii\rest\Controller
{
	private $base_url = "http://login.scout-gps.ru:8081/";
	private $actions = [
		"LOGIN" => "spic/auth/rest/Login",
		"LOGOUT" => "spic/auth/rest/Logout",
		"ALL_UNITS_COUNT" => "spic/units/rest/",
		"ALL_UNITS_PAGE" => "spic/units/rest/getAllUnitsPaged",
		"SUBSCRIBE" => "spic/OnlineDataService/rest/Subscribe",
		"ONLINE_DATA" => "spic/OnlineDataService/rest/GetOnlineData",
	];

	private function send($data, $url, $method = "POST")
	{
		$session_id = \Yii::$app->session->get("session_id");

		$ch = curl_init();
		$options = [
			CURLOPT_URL => $this->base_url . $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
		];

		$options[CURLOPT_CUSTOMREQUEST] = $method;
		$options[CURLOPT_POSTFIELDS] = json_encode(is_null($data) ? [] : $data);

		$headers = [
			"Accept:json",
			"Content-Type:application/json",
		];

		if (!is_null($session_id)) {
			$headers[] = "ScoutAuthorization:{$session_id}";
		}
		$options[CURLOPT_HTTPHEADER] = $headers;

		curl_setopt_array($ch, $options);

		$response = curl_exec($ch);

		if ($errno = curl_errno($ch)) {
			$error_message = curl_strerror($errno);
			\Yii::error("cURL error ({$errno}):\n {$error_message}");
		}

		return json_decode($response, true);
	}

	private function authorization()
	{
		$data = [
			"Login" => "garmayev@yandex.ru",
			"Password" => "12345",
			"TimeStampUtc" => "/Date(" . time() . ")/",
			"CultureName" => "ru-ru",
			"UiCultureName" => "ru-ru"
		];
		return $this->send($data, $this->actions['LOGIN']);
	}

	private function units()
	{
		return $this->send(["Offset" => 0, "Count" => 25], $this->actions["ALL_UNITS_PAGE"]);
	}

	private function getSubscribtionId($unitIds)
	{
		return $this->send(["UnitIds" => $unitIds], $this->actions["SUBSCRIBE"]);
	}

	private function getOnlineData($subscribe)
	{
		return $this->send($subscribe, $this->actions["ONLINE_DATA"]);
	}

	public function actionCars()
	{
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$sessionId = \Yii::$app->session->get("session_id");
		$subscribtionId = \Yii::$app->session->get("subscribtion_id");
		if (!isset($sessionId)) {
			$sessionId = $this->authorization()["SessionId"];
			\Yii::$app->session->set("session_id", $sessionId);
		}
		$ids = [];
		$units = $this->units();
		if (!isset($units)) {
			$sessionId = $this->authorization()["SessionId"];
			\Yii::$app->session->set("session_id", $sessionId);
			$units = $this->units();
		}
		foreach ($units["Units"] as $unit) {
			$ids[] = $unit["UnitId"];
		}
		$subscribtionId = $this->getSubscribtionId($ids);
		if ($subscribtionId) {
			$subscribtionId = $subscribtionId["SessionId"];
			\Yii::$app->session->set("subscribtion_id", $subscribtionId);
		}
		$onlineData = $this->getOnlineData($subscribtionId);
		if (isset($onlineData["OnlineDataCollection"])) {
			$collection = $onlineData["OnlineDataCollection"];
			$dataCollection = $collection["DataCollection"];
			if (isset($dataCollection)) {
				return $dataCollection;
			}
		}
		return [];
	}
}