<?php

use common\models\Client;
use common\models\search\ClientSearch;
use yii\bootstrap\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var $this View
 * @var $clientProvider ActiveDataProvider
 * @var $model Client
 * @var $searchModel ClientSearch
 */

$this->title = "Клиенты";

echo GridView::widget([
	"dataProvider" => $clientProvider,
	"filterModel" => $searchModel,
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
			"attribute" => "notify",
			"label" => "Notify",
			"content" => function ($model) {
				/**
				 * @var $model Client
				 */
//				$form = ActiveForm::begin(["action" => ["/client/update", "id" => $model->id]]);
//				$result = Html::beginForm(["/client/update", "id" => $model->id], 'post', ["class" => 'client-notify-ajax']);
				$result = Html::activeDropDownList($model, 'notify', $model->getNotifyList(), ["class" => "form-control client-notify-ajax", "data-key" => $model->id, "id" => "client-notify-{$model->id}"]);
//				$result .= Html::endForm();
				return $result;
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

$this->registerJs("
$('form').submit((e) => {
	e.preventDefault();
})
$('.client-notify-ajax').on('change', (e) => {
	$.ajax({
		url: '/admin/client/update?id='+$(e.currentTarget).attr('data-key'),
		method: 'post',
		data: {'Client[notify]' :$(e.currentTarget).val()},
	});
})
", View::POS_LOAD);