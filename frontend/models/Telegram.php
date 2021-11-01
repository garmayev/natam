<?php

namespace frontend\models;

class Telegram extends \yii\base\Model
{
	protected $client;

	public $update_id;
	public $message = null;
	public $callback_query = null;
	public $edited_message = null;
	public $my_chat_member = null;

	public function __construct($config = [])
	{
		parent::__construct($config);
		$this->client = new \yii\httpclient\Client();
	}

	public static function sendMessage($args)
	{
		$client = new \yii\httpclient\Client();
		return $client->createRequest()
			->setMethod("POST")
			->setUrl("https://api.telegram.org/bot".\Yii::$app->params["telegram"]["bot_id"]."/sendMessage")
			->setData($args)
			->send();
	}

	public static function editMessage($args)
	{
		$client = new \yii\httpclient\Client();
		return $client->createRequest()
			->setMethod("POST")
			->setUrl("https://api.telegram.org/bot".\Yii::$app->params["telegram"]["bot_id"]."/editMessageText")
			->setData($args)
			->send();
	}

	public static function deleteMessage($args)
	{
		$client = new \yii\httpclient\Client();
		return $client->createRequest()
			->setMethod("POST")
			->setUrl("https://api.telegram.org/bot".\Yii::$app->params["telegram"]["bot_id"]."/deleteMessage")
			->setData($args)
			->send();
	}

	public function send()
	{

	}
}