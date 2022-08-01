<?php

use common\models\Product;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\web\View;
use yii\helpers\Html;


/**
 * @var $this View
 * @var $categoryProvider ActiveDataProvider
 * @var $productProvider ActiveDataProvider
 * @var $model Product
 */

$this->title = Yii::t("app", "Products");

echo Html::a(Yii::t("app", "Append Product"), ["/admin/product/create"], ["class" => ["btn", "btn-success"]]);

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
			"content" => function ($model) {
				return Html::img($model->thumbs, ["width" => "100px"]);
			}
		], [
			"attribute" => "isset",
			"format" => "html",
			"content" => function ($model) {
				return ($model->isset) ? Html::tag("span", "", ["class" => ["fa", "fa-check"]]) : Html::tag("span", "", ["class" => ["fa", "fa-times"]]);
			}
		], [
			'class' => \yii\grid\ActionColumn::className(),
			'headerOptions' => [
				"width" => "80px"
			],
			"template" => "{update} {delete}",
		]
	]
]);