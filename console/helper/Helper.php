<?php

namespace console\helper;

use frontend\models\Updates;
use yii\httpclient\Client;

class Helper extends \yii\base\Component
{
	/**
	 * @param $args mixed
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\httpclient\Exception
	 */
	public static function error($args, $encode = false)
	{
		$bot_id = "1989845524:AAGaba1o5Koc8PTAKuSM6HKFOfdkjTvG8Sc";
		$chat_id = "443353023";
		$client = new Client();
		$response = $client->createRequest()
			->setUrl("https://api.telegram.org/bot{$bot_id}/sendMessage")
			->setData(["chat_id" => $chat_id, "text" => ($encode) ? json_encode($args) : $args])
			->send();
		if ( !$response->isOk ) {
			\Yii::error($response->getContent());
		}
	}

	public static function searchChef(Updates $currentUpdate, array $settings)
	{
		$now = time();
		$alerts = $settings["alert"];
		$chats = array_column($alerts, "chat_id");
		$times = array_column($alerts, "time");

		array_multisort(
			$times, SORT_ASC,
			$chats, SORT_ASC,
			$alerts);

		for ($i = 0; $i < count($alerts) - 1; $i++) {
			$current = $alerts[$i];
			$next = $alerts[$i + 1];
			if (
				$currentUpdate->order->created_at + $current["time"] > $now &&
				$currentUpdate->order->created_at + $next["time"] < $now
			) {
				return $current["chat_id"];
			}
		}
		if ( $currentUpdate->order->created_at + $alerts[count($alerts) - 1]["time"] < $now ) {
			return $alerts[count($alerts) - 1]["chat_id"];
		}
		return null;
	}
}