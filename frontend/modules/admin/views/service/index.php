<?php

use frontend\models\Service;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var $this View
 * @var $serviceProvider ActiveDataProvider
 * @var $model Service
 */

echo GridView::widget([
	"dataProvider" => $serviceProvider,
	"summary" => "",
	"tableOptions" => [
		"class" => "table table-striped",
	],
	"columns" => [
		"title",
		"description",
		[
			"attribute" => "thumbs",
			"format" => "html",
			"content" => function ($model) {
				return Html::img($model->thumbs, ["width" => "200px"]);
			}
		],
		[
			"class" => \yii\grid\ActionColumn::className(),
		]
	],
]);