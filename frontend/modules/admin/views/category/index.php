<?php

use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var $this View
 * @var $categoryProvider ActiveDataProvider
 */

$this->registerCss("
.btn-group .dropdown-menu {
	border: 1px solid #ccc;
	box-shadow: 0px 10px 12px 0 rgba(0, 0, 255, .2);
}
");

$this->title = "Продукты";

echo Html::beginTag("div", ["class" => "btn-group", "role" => "group"]);
echo Html::button("Добавить".Html::tag("span", "", ["class" => "caret", "style" => "margin-left: 15px;"]), ["class" => ["btn", "btn-primary", "dropdown-toggle"], "data-toggle" => "dropdown", "aria-haspopup" => true, "aria-expanded" => false]);
echo Html::beginTag("ul", ["class" => "dropdown-menu"]);
echo Html::tag("li", Html::a("Категорию", ["category/create"]));
echo Html::tag("li", Html::a("Продукт", ["product/create"]));
echo Html::endTag("ul");
echo Html::endTag("div");

echo GridView::widget([
	"dataProvider" => $categoryProvider,
	"summary" => "",
	"columns" => [
		"title",
		"content",
		[
			"attribute" => "thumbs",
			"format" => "html",
			"content" => function ($model) {
				return Html::img($model->thumbs, ["style" => "width: 150px;"]);
			}
		], [
			'class' => \yii\grid\ActionColumn::className(),
			'headerOptions' => [
				"width" => "80px"
			],
			"template" => "{update} {view} {delete}",
		]
	]
]);