<?php

use common\models\Order;
use common\models\search\OrderSearch;
use kartik\select2\Select2;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $model OrderSearch
 */

$this->registerCss(<<< CSS
.field-ordersearch-status > label {
    display: none;
}
.select2-selection__rendered {
    width: 100% !important;
}
.select2-container--krajee {
display: inline-block;
}
CSS)
?>
<a class="btn btn-primary" role="button" data-toggle="collapse" href="#filter" aria-expanded="false"
   aria-controls="filter" style="margin-left: 10px;">
    Фильтр
</a>
<div class="collapse" id="filter">
	<?php
	$form = ActiveForm::begin([
		"action" => 'index',
		"method" => "get",
//		"options" => [
//			"style" => "margin-top: 15px;"
//		]
	]);
	echo $form->field($model, "client_name", ["options" => ["class" => "col-xs-12 col-md-12 col-lg-12"]])->textInput(["placeholder" => Yii::t("app", "Customer`s name")])->label(false);
	echo $form->field($model, "location_title", ["options" => ["class" => "col-xs-12 col-md-12 col-lg-12"]])->textInput(["placeholder" => Yii::t("app", "Address")])->label(false);
	echo $form->field($model, "client_phone", ["options" => ["class" => "col-xs-12 col-md-12 col-lg-12"]])->textInput(["placeholder" => Yii::t("app", "Customer`s phone")])->label(false);

//    echo "<label>Theme MATERIAL (Single)</label>";
	echo $form->field($model, 'status')->widget(Select2::class, [
		'data' => Order::getStatusList(),
		'options' => [
			'multiple' => true,
//			'autocomplete' => 'off',
			'placeholder' => 'Выберите один или несколько статусов',
//			'style' => 'width: 100%'
		],
		'pluginOptions' => [
			'allowClear' => true
		]
	]);

	echo $form->field($model, "created_start", ['options' => ['class' => 'col-xs-12 col-md-6 col-lg-6']])->widget(\kartik\datetime\DateTimePicker::className(), [
		'options' => [
			'placeholder' => "Начать с даты создания",
			'value' => ($model->created_start > 0) ? Yii::$app->formatter->asDatetime($model->created_start, 'php:Y-m-d H:i') : "",
		],
		'convertFormat' => true,
		'pluginOptions' => [
			'minView' => 2,
			'format' => 'php:Y-m-d 00:00',
			'autoclose' => true,
		],
	])->label(false);
	echo $form->field($model, "created_finish", ['options' => ['class' => 'col-xs-12 col-md-6 col-lg-6']])->widget(\kartik\datetime\DateTimePicker::className(), [
		'options' => [
			'placeholder' => "Закончить на дате создания",
			'value' => ($model->created_finish > 0) ? Yii::$app->formatter->asDatetime($model->created_finish, 'php:Y-m-d H:i') : '',
		],
		'convertFormat' => true,
		'pluginOptions' => [
			'minView' => 2,
			'format' => 'php:Y-m-d 23:59',
			'autoclose' => true,
		],
	])->label(false);

	echo $form->field($model, "delivery_start", ['options' => ['class' => 'col-xs-12 col-md-6 col-lg-6']])->widget(\kartik\datetime\DateTimePicker::className(), [
		'options' => [
			'placeholder' => "Начать с даты доставки",
			'value' => ($model->delivery_start > 0) ? Yii::$app->formatter->asDatetime($model->delivery_start, 'php:Y-m-d H:i') : "",
		],
		'convertFormat' => true,
		'pluginOptions' => [
			'minView' => 2,
			'format' => 'php:Y-m-d 00:00',
			'autoclose' => true,
		],
	])->label(false);
	echo $form->field($model, "delivery_finish", ['options' => ['class' => 'col-xs-12 col-md-6 col-lg-6']])->widget(\kartik\datetime\DateTimePicker::className(), [
		'options' => [
			'placeholder' => "Закончить на дате доставки",
			'value' => ($model->delivery_finish > 0) ? Yii::$app->formatter->asDatetime($model->delivery_finish, 'php:Y-m-d H:i') : "",
		],
		'convertFormat' => true,
		'pluginOptions' => [
			'minView' => 2,
			'format' => 'php:Y-m-d 23:59',
			'autoclose' => true,
		],
	])->label(false);

	echo \yii\helpers\Html::a("Сбросить", ["index"], ["class" => "btn btn-default", "style" => "margin-right: 10px"]);
	echo \yii\helpers\Html::submitButton("Применить", ["class" => "btn btn-success", "style" => "margin-right: 10px"]);
	ActiveForm::end();
	?>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        $(".export").on("click", (e) => {
            e.preventDefault();
            $(e.currentTarget).closest("form").attr("action", "export").submit();
        })
    });
</script>