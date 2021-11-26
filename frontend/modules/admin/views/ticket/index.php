<?php

use common\models\Ticket;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\helpers\Html;


/**
 * @var $this View
 * @var $ticketProvider ActiveDataProvider
 */

echo \yii\grid\GridView::widget([
	"summary" => "",
	"dataProvider" => $ticketProvider,
	"columns" => [
		"client.name",
		"client.phone",
		[
			"attribute" => "status",
			"label" => Yii::t("app", "Status"),
			"format" => "raw",
			"content" => function ($data) {
				if ($data === Ticket::STATUS_OPEN) {
					return "Открыт";
				}
				return "Закрыт";
			}
		],
		"comment",
		[
			"attribute" => "service_id",
			"label" => Yii::t("app", "Services"),
			"content" => function ($data) {
				if ( !empty($data->service_id) ) {
					$service = \frontend\models\Service::findOne($data->service_id);
					return $service->title;
				} else {
					return "Общие вопросы";
				}
			}
		], [
			'class' => \yii\grid\ActionColumn::className(),
			'headerOptions' => ["width" => '80'],
			'template' => '{view} {update} {delete}'
		]
	]
]);