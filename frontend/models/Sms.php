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
			->setUrl("https://api.prostor-sms.ru/messages/v2/send")
			->addHeaders(["Authorization" => "Basic ".base64_encode("ak141747:914042")])
			->setData([
				"phone" => "+{$phone}",
				"text" => $text,
				"sender" => "Natam Trade",
			])
			->send();
		Yii::error($response->getContent());
		if ($response->isOk) {
			Yii::error($response->getData());
		}
	}
}