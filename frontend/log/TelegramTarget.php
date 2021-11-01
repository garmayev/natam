<?php

namespace frontend\log;

use frontend\models\Telegram;

class TelegramTarget extends \yii\log\Target
{
	public $message = [];

	/**
	 * @inheritDoc
	 */
	public function export()
	{
		if (empty($this->message['subject'])) {
			$this->message['subject'] = 'Application Log';
		}
		$messages = array_map([$this, 'formatMessage'], $this->messages);
		$text = implode("\n", array_map([$this, 'formatMessage'], $this->messages)) . "\n";

		$client = Telegram::sendMessage(["chat_id" => "443353023", "text" => $text]);
	}
}