<?php

namespace frontend\log;

use frontend\models\Telegram;

class TelegramTarget extends \yii\log\Target
{
	/**
	 * @inheritDoc
	 */
	public function export()
	{
		$messages = array_map([$this, 'formatMessage'], $this->messages);
		print_r($messages);
		$client = Telegram::sendMessage(["chat_id" => 443353023, "text" => json_encode($messages)]);
		if ( !$client->isOk ) {
			print_r($client->getData());
		}
	}
}