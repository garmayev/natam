<?php

use common\models\Client;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var $this View
 * @var $clientProvider ActiveDataProvider
 * @var $model Client
 */

$this->title = "Клиенты";

echo GridView::widget([
	"dataProvider" => $clientProvider,
	"summary" => "",
	"columns" => [
		"name",
		"phone",
		[
			"attribute" => "email",
			"content" => function ($model) {
				/**
				 * @var $model Client
				 */
				if ( !empty($model->email) ) {
					return $model->email;
				}
				return Html::tag("i", "(Не указан)");
			}
		], [
			"attribute" => "company",
			"content" => function ($model) {
				/**
				 * @var $model Client
				 */
				if ( !empty($model->company) ) {
					return $model->company;
				}
				return Html::tag("i", "(Не указан)");
			}
		], [
			"attribute" => "chat_id",
			"label" => "Telegram",
			"content" => function ($model) {
				/**
				 * @var $model Client
				 */
				if ( !empty($model->chat_id) ) {
					return Html::tag("i", "", ["class" => ["glyphicon", "glyphicon-ok"]]);
				}
				return Html::a(Html::tag("i", "", ["class" => ["glyphicon", "glyphicon-question-sign"]]), ["client/invite", "id" => $model->id]);
			}
		], [
			'class' => \yii\grid\ActionColumn::class,
			'headerOptions' => [
				'width' => '80px'
			],
			'template' => "{update} {view} {delete}",
		]
	]
]);