<?php

use frontend\models\Product;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var $productProvider ActiveDataProvider
 * @var $model Product
 */

echo GridView::widget([
	"dataProvider" => $productProvider,
	"summary" => "",
	"columns" => [
		"title",
		"description",
		"price",
		"value",
		[
			"attribute" => "thumbs",
			"format" => "html",
			"value" => function ($model)
			{
				return Html::img($model->thumbs, ["style" => "height: 100px;"]);
			}
		]
	]
]);