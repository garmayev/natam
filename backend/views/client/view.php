<?php

use common\models\Client;
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
			"label" => Yii::t("app", "Customer`s phone"),
		],[
			"attribute" => "email",
			"label" => Yii::t("app", "Customer`s email"),
			"format" => "html",
			"value" => call_user_func(function ($model) {
				/**
				 * @var $model Client
				 */
				if ( !empty($model->email) ) {
					return $model->email;
				}
				return Html::tag("i", "(Не указан)");
			}, $model)
		], [
			"attribute" => "company",
			"label" => Yii::t("app", "Customer`s company"),
			"format" => "html",
			"value" => call_user_func(function ($model) {
				/**
				 * @var $model Client
				 */
				if ( !empty($model->company) ) {
					return $model->company;
				}
				return Html::tag("i", "(Не указана)");
			}, $model)
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