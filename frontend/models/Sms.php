<?php

namespace frontend\models;

use Yii;

class Sms extends \yii\base\Model
{
	public $text;
	public $phone;

	public static function send($text, $phone)
	{
		$client = new \yii\httpclient\Client();
		$response = $client->createRequest()
			->setMethod("GET")
			->setUrl("https://sms.ru/sms/send")
			->setData([
				"api_id" => "F1F520FA-F7CA-4EC7-44C6-71B9D7B07372",
				"to" => $phone,
				"msg" => $text,
				"json" => 1,
			])
			->send();
		if (!$response->isOk) {
			Yii::error($response);
		}
	}
}