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

	private function send($data, $url, $session_id = null, $method = "POST")
	{
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
			"TimeStampUtc" => "/Date(" . (time() + 10000) . ")/",
			"TimeZoneOlsonId" => "Aisa/Irkutsk",
			"CultureName" => "ru-ru",
			"UiCultureName" => "ru-ru"
		];
		return $this->send($data, $this->actions['LOGIN']);
	}

	private function units($session_id)
	{
		return $this->send(["Offset" => 0, "Count" => 25], $this->actions["ALL_UNITS_PAGE"], $session_id);
	}

	private function getSubscribtionId($unitIds, $session_id)
	{
		return $this->send(["UnitIds" => $unitIds], $this->actions["SUBSCRIBE"], $session_id);
	}

	private function getUnitsCount($session_id) 
	{
		return $this->send(null, $this->actions["ALL_UNITS_COUNT"], $session_id);
	}

	private function getOnlineData($subscribe, $session_id)
	{
		return $this->send(["Id" => $subscribe], $this->actions["ONLINE_DATA"], $session_id);
	}

	public function actionCars()
	{
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$auth = $this->authorization();
		\Yii::error($auth);
		$authToken = $auth["SessionId"];
		$ids = [];
		$units = $this->units($authToken);
		if (isset($units)) {
			foreach ($units["Units"] as $unit) {
				$ids[] = $unit["UnitId"];
			}
			$subscribe = $this->getSubscribtionId($ids, $authToken)["SessionId"]["Id"];
			$onlineData = $this->getOnlineData($subscribe, $authToken);
			if (isset($onlineData["OnlineDataCollection"])) {
				$collection = $onlineData["OnlineDataCollection"];
				$dataCollection = $collection["DataCollection"];
				if (isset($dataCollection)) {
					return $dataCollection;
				}
			}
		}
		return [];
	}
	
	public function actionLogout()
	{
		
	}
}