<?php

use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ListView;


/**
 * @var $this View
 * @var $vacancyProvider ActiveDataProvider
 */

//$this->registerJsFile("/js/about.js");

echo Html::beginTag("section");
echo Html::beginTag("div", ["class" => "container"]);

echo ListView::widget([
	"dataProvider" => $vacancyProvider,
	"summary" => "",
	"itemView" => "_vacancy",
	"options" => [
		"tag" => "div",
		"class" => "news_slider"
	],
	"itemOptions" => [
		"tag" => "div",
		"class" => "news_item"
	],
]);

echo Html::endTag("div");
echo Html::endTag("section");

$this->registerCss(".vacancy_inner { display: flex; flex-direction: row; padding-top: 40px; } .vacancy_item {width: 50%; }");