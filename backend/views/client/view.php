<?php

use common\models\Client;
use common\models\Order;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var $this View
 * @var $model Client|null
 */

$this->title = $model->name;

echo DetailView::widget([
	"model" => $model,
	"template" => "<tr><th style='width: 20%'>{label}</th><td>{value}</td></tr>",
	"attributes" => [
		[
			"attribute" => "name",
			"label" => Yii::t("app", "Customer`s name"),
		], [
			"attribute" => "phone",
			"format" => "html",
			"label" => Yii::t("app", "Customer`s phone"),
			"value" => function (Client $model) {
				return Html::a($model->phone, "tel:+{$model->phone}");
			}
		],[
			"attribute" => "email",
			"label" => Yii::t("app", "Customer`s email"),
			"format" => "html",
			"value" => function (Client $model) {
				if ( !empty($model->email) ) {
					return $model->email;
				}
				return Html::tag("p", Yii::t('yii', '(not set)'), ['class' => 'not-set']);
			}
		], [
			"attribute" => "company_id",
			"label" => Yii::t("app", "Customer`s company"),
			"format" => "html",
			"value" => function (Client $model) {
				if ( !empty($model->company_id) ) {
					return Html::a($model->organization->title, ["company/view", "id" => $model->company_id]);
				}
				return Html::tag("p", Yii::t('yii', '(not set)'), ['class' => 'not-set']);
			}
		],[
			"attribute" => "chat_id",
			"label" => Yii::t("app", "Telegram"),
			"format" => "html",
			"value" => call_user_func(function ($model) {
				/**
				 * @var $model Client
				 */
				if ( !empty($model->chat_id) ) {
					return Html::tag("i", "", ["class" => ["glyphicon", "glyphicon-ok"]]);
				}
				return Html::a("Пригласить в Telegram-бот", ["client/invite", "id" => $model->id]);
			}, $model)
		]
	]
]);

echo GridView::widget([
	"dataProvider" => new ArrayDataProvider([
		"allModels" => $model->orders
	]),
	"summary" => "",
	"columns" => [
		[
			"attribute" => "id",
			"format" => "html",
			"value" => function (Order $model) {
				return Html::a("#{$model->id}", ["order/view", "id" => $model->id]);
			}
		], [
			"attribute" => "location_id",
			"label" => Yii::t('app', 'Address'),
			"format" => "html",
			"value" => function (Order $model) {
				if ( isset($model->location_id) ) {
					return Html::a($model->location->title, ["location/view", "id" => $model->location_id]);
				}
				return Html::tag("p", $model->address);
			}
		], 'price', [
			"attribute" => "comment",
			"format" => "html",
			"value" => function (Order $model) {
				if ( isset($model->comment) && $model->comment != '' ) {
					return Html::tag('div', $model->comment);
				}
				return Html::tag("p", Yii::t("yii", "(not set)"), ["class" => "not-set"]);
			}
		], [
			"attribute" => "status",
			"value" => function (Order $model) {
				return $model->getStatusName();
			}
		], [
			"attribute" => "created_at",
			"value" => function (Order $model) {
				return Yii::$app->formatter->asDatetime($model->created_at, "php:d F Y H:i");
			}
		], [
			"attribute" => "delivery_date",
			"value" => function (Order $model) {
				return Yii::$app->formatter->asDatetime($model->delivery_date, "php:d F Y H:i");
			}
		]
	]
]);