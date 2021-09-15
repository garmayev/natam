<?php

use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ListView;


/**
 * @var $this View
 * @var $vacancyProvider ActiveDataProvider
 */

echo Html::beginTag("section", ["class" => "about"]);
echo Html::beginTag("div", ["class" => "container"]);

echo ListView::widget([
	"dataProvider" => $vacancyProvider,
	"summary" => "",
	"itemView" => "_vacancy",
	"itemOptions" => [
		"class" => "vacancy_item"
	],
	"options" => [
		"class" => "vacancy_inner"
	]
]);

echo Html::endTag("div");
echo Html::endTag("section");

$this->registerCss(".vacancy_inner { display: flex; flex-direction: row; padding-top: 40px; } .vacancy_item {width: 50%; }");