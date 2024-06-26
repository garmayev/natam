<?php

namespace frontend\commands;

use Yii;

class Command extends \aki\telegram\base\Command
{
	public static function run($command, callable $fun)
	{
		$text = '';
		$telegram = Yii::$app->telegram;
		if ( isset($telegram->input->message->text) ) {
			$text = $telegram->input->message->text;
		} else {
			if (isset($telegram->input->callback_query))
			$text = $telegram->input->callback_query->data;
		}
		$args = explode(' ', $text);
		$inputCommand = array_shift($args);
		if($inputCommand === $command){
			if ( count($args) ) {
				parse_str($args[0], $argument);
				return call_user_func_array($fun, [$telegram, $argument]);
			}
			return call_user_func_array($fun, [$telegram]);
		}
	}
}