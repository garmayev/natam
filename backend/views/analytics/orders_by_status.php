<?php

use common\models\Settings;
use common\models\TelegramMessage;
use garmayev\staff\models\Employee;
use yii\web\View;
use yii\helpers\Html;

/**
 * @var $this View
 * @var $messages TelegramMessage[]
 */

foreach ($messages as $message) {
//	if ( $message->isExpired($message->getTimeElapsed()) ) {
		echo Html::beginTag("div", [
			"class" => [
				"panel",
				isset($_GET["expired"]) ? "panel-danger" : "panel-success"
			]
		]);
		echo Html::tag("div", Html::a("Заказ #{$message->order_id}", ["order/view", "id" => $message->order_id]), ["class" => "panel-heading"]);
		echo Html::beginTag("div", ["class" => "panel-body"]);
		echo Html::tag("p", "Прошло времени: " . Yii::$app->formatter->asDuration($message->updated_at - $message->created_at));
		echo Html::endTag("div");
		echo Html::endTag("div");
//	}
}