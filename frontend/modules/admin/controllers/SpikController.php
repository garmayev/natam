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
		$now = time();
		$data = [
			"Login" => "garmayev@yandex.ru",
			"Password" => "12345",
			"TimeStampUtc" => "/Date(" . ($now + 10000) . ")/",
			"TimeZoneOlsonId" => "Aisa/Irkutsk",
			"CultureName" => "ru-ru",
			"UiCultureName" => "ru-ru"
		];
		$authExpire = \Yii::$app->session->get("authExpire");
		if ( isset($authExpire) ) {
			if ($now > $authExpire) {
				\Yii::error("Auth token is die! Generate new Token");
			} else {
				return \Yii::$app->session->get("authToken");
			}
		}
		$response = $this->send($data, $this->actions['LOGIN']);
		if ($response["IsAuthenticated"] && $response["IsAuthorized"]) {
			preg_match("/Date\(([0-9]*)/", $response["ExpireDate"], $matches);
			$expire = (intval($matches[1]) / 1000) + (3 * 3600);
			$authToken = $response["SessionId"];
			\Yii::$app->session->set("authToken", $authToken);
			\Yii::$app->session->set("authExpire", $expire);
			return $authToken;
		} else {
			return null;
		}
	}

	private function units($session_id)
	{
		$response = $this->send(["Offset" => 0, "Count" => 25], $this->actions["ALL_UNITS_PAGE"], $session_id);
		\Yii::error($response);
		return $response;
	}

	private function getSubscribtionId($unitIds, $session_id)
	{
		if ( $subscribeToken = \Yii::$app->session->get("subscribeToken") ) {
			return $subscribeToken;
		} else {
			$response = $this->send(["UnitIds" => $unitIds], $this->actions["SUBSCRIBE"], $session_id);
			if ( count($response["State"]["ErrorCodes"]) > 0 ) {
				\Yii::error($response);
				return null;
			} else {
				$subscribeToken = $response["SessionId"]["Id"];
				\Yii::$app->session->set("subscribeToken", $subscribeToken);
				return $subscribeToken;
			}
		}
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
//		\Yii::$app->session->remove("authToken");
//		\Yii::$app->session->remove("authExpire");
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$authToken = $this->authorization();
		if ( is_null($authToken) ) return ["ok" => false, "data" => "Authorization token is die"];
		$ids = [];
		$units = $this->units($authToken);
		if (isset($units)) {
			foreach ($units["Units"] as $unit) {
				$ids[] = $unit["UnitId"];
			}
			$subscribeToken = $this->getSubscribtionId($ids, $authToken);
			if ( is_null($subscribeToken) ) return ["ok" => false, "data" => "Subscribe is die"];
			$onlineData = $this->getOnlineData($subscribeToken, $authToken);
			if (isset($onlineData["OnlineDataCollection"])) {
				$collection = $onlineData["OnlineDataCollection"];
				$dataCollection = $collection["DataCollection"];
				if (isset($dataCollection)) {
					return ["ok" => true, "data" => $dataCollection];
				}
			}
		} else {
			\Yii::error($units);
		}
		return ["ok" => false, "data" => "Units is null"];
	}
	
	public function actionLogout()
	{
		
	}
}