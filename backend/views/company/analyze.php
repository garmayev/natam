<?php

use common\models\Company;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $model Company
 */
echo Html::a(Yii::t('app', 'Search'), '#', ['class' => ['btn', 'btn-primary']]);

$form = ActiveForm::begin();
echo Html::hiddenInput('id', $model->id);
ActiveForm::end();

$this->registerJs(<<< JS
$(() => {
	$(".btn").on("click", (e) => {
        e.preventDefault();
		var url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/party";
		var token = "2c9418f4fdb909e7469087c681aac4dd7eca158c";
		var query = `$model->title`;
		
		var options = {
		    method: "POST",
		    mode: "cors",
		    headers: {
		        "Content-Type": "application/json",
		        "Accept": "application/json",
		        "Authorization": "Token " + token
		    },
		    body: JSON.stringify({query: query})
		}
		
		fetch(url, options)
		.then(response => response.text())
		.then(result => console.log(result))
		.catch(error => console.log("error", error));
    })
})
JS
	, View::POS_LOAD);