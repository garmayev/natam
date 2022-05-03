<?php

use common\models\Category;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var $this View
 * @var $model Category
 * @var $productProvider ActiveDataProvider
 */

$this->title = "{$model->title}";

echo Html::a(Yii::t("app", "New Product"), ["product/create"], ["class" => ["btn", "btn-primary"], "style" => "margin-right: 10px;"]);
echo Html::a(Yii::t("app", "Cancel"), ["category/index"], ["class" => ["btn", "btn-danger"]]);

echo GridView::widget([
	"dataProvider" => $productProvider,
	"summary" => "",
	"columns" => [
		"title",
		"price",
		"value",
		[
			"attribute" => "isset",
			"headerOptions" => [
				"style" => "width: 150px",
			],
			"content" => function ($model) {
				return (!$model->isset) ? Html::tag("i", "", ["class" => ["glyphicon", "glyphicon-ok"]]) : Html::tag("i", "", ["class" => ["glyphicon", "glyphicon-remove"]]);
			}
		], [
			'class' => \yii\grid\ActionColumn::className(),
			'headerOptions' => [
				"width" => "80px"
			],
			"template" => "{update} {delete}",
			"buttons" => [
				"update" => function ($url, $model) {
					$url = Url::to(["/admin/product/update", "id" => $model->id]);
					return Html::a(Html::tag("i", "", ["class" => ["glyphicon", "glyphicon-pencil"]]), $url);
				},
				"view" => function ($url, $model) {
					$url = Url::to(["/admin/product/view", "id" => $model->id]);
					return Html::a(Html::tag("i", "", ["class" => ["glyphicon", "glyphicon-eye-open"]]), $url);
				},
				"delete" => function ($url, $model) {
					$url = Url::to(["/admin/product/delete", "id" => $model->id]);
					return Html::a(Html::tag("i", "", ["class" => ["glyphicon", "glyphicon-trash"]]), $url);
				}
			]
		],
	]
]);