<?php

use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ListView;


/**
 * @var $this View
 * @var $productProvider ActiveDataProvider
 */
echo Html::beginTag("div", ["class" => "product", "style" => "padding-top: 25px; min-height: 60vh;"]);
echo Html::beginTag("div", ["class" => "container"]);
echo ListView::widget([
	"dataProvider" => $productProvider,
	"itemView" => "_product",
	"summary" => "",
	"options" => [
		"tag" => "div",
		"class" => "product_inner"
	],
	"itemOptions" => [
		"tag" => "div",
		"class" => "product_item"
	]
]);
echo Html::endTag("div");
echo Html::endTag("div");