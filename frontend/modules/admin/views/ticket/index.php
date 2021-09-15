<?php

use frontend\models\Ticket;
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
			"label" => "Status",
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
			'class' => \yii\grid\ActionColumn::className(),
			'headerOptions' => ["width" => '80'],
			'template' => '{view} {update} {delete}'
		]
	]
]);