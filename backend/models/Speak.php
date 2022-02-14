<?php

namespace backend\models;

use yii\base\Model;
use yii\httpclient\Client;

class Speak extends Model
{
	public static function authorize()
	{
		$client = new Client();
		$response = $client->createRequest()
			->setMethod("POST")
			->setUrl("http://spic.scout365.ru:8081/spic/auth/rest/Login")
			->setData([
				'Login' => 'garmayev@yandex.ru',
				'Password' => '12345',
				'TimeStampUtc' => '/Date('.mktime().')/',
				'TimeZoneOlsonId' => 'Asia/Irkutsk',
				'CultureName' => 'ru-ru',
				'UiCultureName' => 'ru-ru'
			])
			->send();
		\Yii::error($response->isOk);
		if ( $response->isOk ) {
			\Yii::error($response->data);
		}
	}
}