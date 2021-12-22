<?php

use common\models\search\OrderSearch;
use kartik\export\ExportMenu;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\helpers\Html;


/**
 * @var $this View
 * @var $orderProvider ActiveDataProvider
 * @var $searchModel OrderSearch
 */

$this->title = Yii::t("app", "Orders");

echo Html::a(Yii::t("app", "New Order"), ["order/create"], ["class" => ["btn", "btn-success"], "style" => "margin-right: 10px;"]);

$gridColumns = [
	'client.name',
	'client.phone',
	[
		"attribute" => "location.title",
		"label" => Yii::t("app", "Address"),
		"format" => "html",
		"enableSorting" => true,
		"content" => function ($model, $key) {
			if ($model->location) return $model->location->title;
			return $model->address;
		}
	], [
		"attribute" => "comment",
	],
	"price",
	[
		"attribute" => "status",
		"content" => function ($model, $key) {
			return $model->getStatus($model->status);
		}
	], [
		"attribute" => "created_at",
		"label" => Yii::t("app", "Created At"),
		"content" => function ($model, $key) {
			return Yii::$app->formatter->asDatetime($model->created_at, "php:d M Y H;i");
		}
	], [
		"attribute" => "delivery_date",
		"content" => function ($model) {
			return Yii::$app->formatter->asDatetime($model->delivery_date, "php:d M Y H:i");
		}
	],
];
echo ExportMenu::widget([
	'dataProvider' => $orderProvider,
	'columns' => $gridColumns
]);

echo $this->render("_search", ["model" => $searchModel]);

echo \yii\grid\GridView::widget([
	"dataProvider" => $orderProvider,
	"summary" => "",
//	"filterModel" => $searchModel,
	"columns" => [
		[
			"attribute" => "id",
			"headerOptions" => ["style" => "width: 4%;"]
		], [
			"attribute" => "client.name",
			"label" => Yii::t("app", "Customer`s name"),
			"enableSorting" => true,
			"headerOptions" => ["style" => "width: 15%;"]
		], [
			"attribute" => "client.phone",
			"label" => Yii::t("app", "Customer`s phone"),
			"enableSorting" => true,
			"headerOptions" => ["style" => "width: 10%;"]
		], [
			"attribute" => "location.title",
			"label" => Yii::t("app", "Address"),
			"format" => "html",
			"enableSorting" => true,
			"content" => function ($model, $key) {
				if ($model->location) return $model->location->title;
				return null;
			}
		], [
			"attribute" => "comment",
			"format" => "html",
			"content" => function ($model, $key) {
				return Html::activeTextarea($model, "comment", ["rows" => 4, "width" => "100%", "style" => "box-sizing: border-box; width: 100%;", "data-key" => $key, "id" => "comment-{$key}", "class" => "comment form-control"]);
			}
		],
		"price",
		[
			"attribute" => "status",
			"format" => "html",
			"content" => function ($model, $key) {
				return Html::activeDropDownList($model, "status", $model->getStatus(), ["style" => "box-sizing: border-box; width: 100%;", "data-key" => $key, "id" => "status-{$key}", "class" => "status form-control"]);
			}
		], [
			"attribute" => "created_at",
			"content" => function ($model, $key) {
				return Yii::$app->formatter->asDatetime($model->created_at, "php:d M Y H;i");
			}
		], [
			"attribute" => "delivery_date",
			"content" => function ($model) {
				return Yii::$app->formatter->asDatetime($model->delivery_date, "php:d M Y H:i");
			}
		], [
			'class' => \yii\grid\ActionColumn::className(),
			'headerOptions' => ["width" => '80'],
			'template' => '{view} {update} {delete}'
		]
	],
	"tableOptions" => [
		"class" => "table table-striped table-bordered",
	]
]);

$this->registerJs("$('.comment').on('blur', function (e) {
	$.ajax({
		url: '/admin/order/update-comment',
		method: 'get',
		data: {comment: $(e.currentTarget).val(), id: $(e.currentTarget).attr('data-key')} 
	});
});
$('.status').on('change', function (e) {
	let target = $(e.currentTarget);
		$.ajax({
			url: '/admin/order/update-status',
			method: 'get',
			data: {status: target.val(), id: target.attr('data-key')},
		}).done(function (response) {
			if ( response.ok ) {
				console.log(typeof target.val());
				if ( (target.val() === '3') || target.val() === '4' ){
					$('tr[data-key='+target.attr('data-key')+']').hide();
				}
			}
		});
});
$('td').on('click', function (e) {
	console.log(e);
})", View::POS_LOAD);