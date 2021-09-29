<?php

namespace console\models;

use frontend\models\Order;

class Alert extends \yii\base\Model
{
	public static function sortAlerts()
	{
		$alerts = \Yii::$app->params["notify"]["alert"];
		$chats = array_column($alerts, "chat_id");
		$times = array_column($alerts, "time");

		array_multisort(
			$times, SORT_ASC,
			$chats, SORT_ASC,
			$alerts);

		return $alerts;
	}

	public static function findChat($time)
	{
		$sorted = Alert::sortAlerts();
//		if ( $sorted[0]["time"] < $time ) return $sorted[0];
		for ($i = 0; $i < count($sorted) - 1; $i++) {
			if ( $time > $sorted[$i]["time"] && $time < $sorted[$i+1]["time"] ) {
				return $sorted[$i];
			}
		}
		if ( $time > $sorted[count($sorted) - 1]["time"] ) {
			return $sorted[count($sorted) - 1];
		}
//		return $sorted[count($sorted) - 1];
	}
}