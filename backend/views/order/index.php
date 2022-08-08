<?php

use common\models\Order;
use common\models\search\OrderSearch;
use kartik\date\DatePicker;
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

if (Yii::$app->user->can("employee")) {
//	echo $this->render("_search", ["model" => $searchModel]);
}
//Yii::error(Yii::$app->formatter->asDate(Yii::$app->params['startDate'], 'y-MM-dd'));
$columns = [
	[
		"attribute" => "id",
		"headerOptions" => ["style" => "width: 4%;"],
		"format" => "html",
		"filter" => Html::activeTextInput($searchModel, 'id', ['class' => 'form-control']),
		"value" => function (Order $model) {
			return Html::a("#{$model->id}", ["order/view", "id" => $model->id]);
		},
		"visible" => true,
	], [
		"attribute" => "client.name",
		"label" => Yii::t("app", "Customer`s name"),
		"enableSorting" => true,
		"headerOptions" => ["style" => "width: 15%;"],
		"filter" => Html::activeTextInput($searchModel, 'client_name', ['class' => 'form-control']),
		"format" => "html",
		"value" => function (Order $model) {
			return Html::a($model->client->name, ["client/view", "id" => $model->client_id]);
		},
	], [
		"attribute" => "client.phone",
		"label" => Yii::t("app", "Customer`s phone"),
		"enableSorting" => true,
		"headerOptions" => ["style" => "width: 10%;"],
		"filter" => Html::activeTextInput($searchModel, 'client_phone', ['class' => 'form-control']),
		"format" => "html",
		"value" => function (Order $model) {
			return Html::a($model->client->phone, "tel:+{$model->client->phone}");
		},
		"visible" => Yii::$app->user->can("employee"),
	], [
		"attribute" => "location.title",
		"label" => Yii::t("app", "Address"),
		"format" => "html",
		"enableSorting" => true,
		"filter" => Html::activeTextInput($searchModel, 'location_title', ['class' => 'form-control']),
		"content" => function (Order $model, $key) {
			if ($model->location) return Html::a($model->location->title, ["location/view", "id" => $model->location_id]);
			return "<i class='not-set'>Самовывоз</i>";
		}
	], [
		"attribute" => "comment",
		"format" => "html",
		"content" => function (Order $model, $key) {
			if ( isset($model->comment) && $model->comment != '' ) {
				return Html::tag('div', $model->comment);
			}
			return Html::tag("p", Yii::t("yii", "(not set)"), ["class" => "not-set"]);
		}
	],
	"price",
	[
		"attribute" => "status",
		"format" => "html",
		"content" => function (Order $model, $key) {
			return Html::tag("p", $model->getStatusName());
		}
	], [
		"attribute" => "created_at",
		'filter' => DatePicker::widget([
			'name' => 'OrderSearch[created_start]',
			'value' => (empty($searchModel->created_start)) ? Yii::$app->formatter->asDate(Yii::$app->params['startDate'], 'y-MM-dd') : Yii::$app->formatter->asDate($searchModel->created_start, 'y-MM-dd'),
			'type' => DatePicker::TYPE_RANGE,
			'name2' => 'OrderSearch[created_finish]',
			'value2' => (empty($searchModel->created_finish)) ? Yii::$app->formatter->asDate(strtotime(OrderSearch::TIME_REMAIN), 'y-MM-dd') : Yii::$app->formatter->asDate($searchModel->created_finish, 'y-MM-dd'),
			'separator' => '-',
			'pluginOptions' => [
				'autoclose' => true,
				'format' => 'yyyy-mm-dd'
			]
		]),
		"content" => function ($model, $key) {
			return Yii::$app->formatter->asDatetime($model->created_at, "php:d F Y H:i");
		}
	], [
		"attribute" => "delivery_date",
		'filter' => DatePicker::widget([
			'name' => 'OrderSearch[delivery_start]',
			'value' => (empty($searchModel->delivery_start)) ? Yii::$app->formatter->asDate(Yii::$app->params['startDate'], 'Y-MM-dd') : Yii::$app->formatter->asDate($searchModel->delivery_start, 'Y-MM-dd'),
			'type' => DatePicker::TYPE_RANGE,
			'name2' => 'OrderSearch[delivery_finish]',
			'value2' => (empty($searchModel->delivery_finish)) ? Yii::$app->formatter->asDate(time(), 'Y-MM-dd') : Yii::$app->formatter->asDate($searchModel->delivery_finish, 'Y-MM-dd'),
			'separator' => '-',
			'pluginOptions' => [
				'autoclose' => true,
				'format' => 'yyyy-mm-dd'
			]
		]),
		"content" => function ($model) {
			return Yii::$app->formatter->asDatetime($model->delivery_date, "php:d F Y H:i");
		}
	], [
		'class' => \yii\grid\ActionColumn::className(),
		'headerOptions' => ["width" => '80'],
		'template' => '{view} {update} {delete}'
	]
];

echo \yii\grid\GridView::widget([
	"dataProvider" => $orderProvider,
	"summary" => "",
	"filterModel" => $searchModel,
	"columns" => $columns,
	"tableOptions" => [
		"class" => "table table-striped table-bordered",
	],
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
", View::POS_LOAD);