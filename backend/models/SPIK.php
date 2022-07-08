<?php

namespace backend\models;

use yii\httpclient\Client;

class SPIK extends \yii\base\Model
{
//	public $client;
	public $response;
	public $token;

	protected $login = "garmayev@yandex.ru";
	protected $password = "12345";

	public function init()
	{
//		$this->client = new Client();
		$this->response = (new Client())->createRequest()
			->setMethod("POST")
			->addHeaders([
				"Accept" => "json",
				"Content-Type" => "application/json"
			]);
		if ( !empty($this->token) ) {
			$this->response->addHeaders([
				"ScoutAuthorization" => $this->token
			]);
		}
		parent::init();
	}

	public function login()
	{
		$result = $this->response
			->setData(json_encode([
				"Login" => $this->login,
				"Password" => $this->password,
				"TimeStampUtc" => "/Date(".(time() * 1000).")/",
				"TimeZoneOlsonId" => 'Asia/Irkutsk',
				"CultureName" => 'ru-RU',
				"UiCultureName" => 'ru-RU'
			]))
			->setOptions([
				CURLOPT_SSL_VERIFYHOST => false
			])
			->setUrl("http://login.scout-gps.ru:8081/spic/auth/rest/Login")
			->send();
		if ( $result->isOk ) {
			$this->token = $result->getData()["SessionId"];
		}
		return $this->token;
	}
}