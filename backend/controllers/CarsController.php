<?php

namespace backend\controllers;

use DateTime;
use yii\web\Response;

class CarsController extends \yii\rest\Controller
{
	private $base_url = "http://login.scout-gps.ru:8081/";
	private $actions = [
		"LOGIN" => "spic/auth/rest/Login",
		"LOGOUT" => "spic/auth/rest/Logout",
		"ALL_UNITS_COUNT" => "spic/units/rest/",
		"ALL_UNITS_PAGE" => "spic/units/rest/getAllUnitsPaged",
		"SUBSCRIBE" => "spic/OnlineDataService/rest/Subscribe",
		"ONLINE_DATA" => "spic/OnlineDataService/rest/GetOnlineData",
		"ONLINE_DATA_WITH_SENSOR" => "spic/OnlineDataWithSensorsService/rest/GetOnlineData",
	];

	public function beforeAction($action)
	{
		return parent::beforeAction($action);
	}

	private function send($data, $url, $session_id = null, $method = "POST")
	{
		$ch = curl_init();
		$headers = [
			"Accept:json",
			"Content-Type:application/json",
		];

		if (!is_null($session_id)) {
			$headers[] = "ScoutAuthorization:{$session_id}";
		}

		$options = [
			CURLOPT_URL => $this->base_url . $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => json_encode(is_null($data) ? [] : $data),
			CURLOPT_HTTPHEADER => $headers
		];

		curl_setopt_array($ch, $options);

		$response = curl_exec($ch);

		if ($errno = curl_errno($ch)) {
			$error_message = curl_strerror($errno);
			\Yii::error("cURL error ({$errno}):\n {$error_message}");
		}
		// \Yii::error($response);
		return json_decode($response, true);
	}

	public function actionLogin()
	{
		\Yii::$app->response->format = Response::FORMAT_JSON;
		return $this->send([
			"Login" => "garmayev@yandex.ru",
			"Password" => "12345",
			"TimeStampUtc" => "/Date(" . time() . ")/",
			"TimeZoneOlsonId" => "Aisa/Irkutsk",
			"CultureName" => "ru-ru",
			"UiCultureName" => "ru-ru"
		], $this->actions["LOGIN"]);
	}

	public function actionUnits($token)
	{
		\Yii::$app->response->format = Response::FORMAT_JSON;
		return $this->send([
			"Offset" => 0,
			"Count" => 25
		], $this->actions["ALL_UNITS_PAGE"], $token);
	}

	public function actionSubscribe($token, $ids)
	{
		\Yii::$app->response->format = Response::FORMAT_JSON;
		return $this->send([
			"UnitIds" => json_decode($ids),
		], $this->actions["SUBSCRIBE"], $token);
	}

	public function actionOnline($token, $subscribe)
	{
		\Yii::$app->response->format = Response::FORMAT_JSON;
		return $this->send([
			"Id" => $subscribe
		], $this->actions["ONLINE_DATA"], $token);
	}
}