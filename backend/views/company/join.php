<?php

use common\models\Client;
use common\models\Company;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\helpers\Html;
use kartik\form\ActiveForm;

/**
 * @var $this View
 * @var $model Company
 */

$form = ActiveForm::begin(['method' => 'GET']);
echo Html::hiddenInput('id', $model->id);
echo Html::dropDownList('client_id', null, ArrayHelper::map(Client::find()->where(['company_id' => null])->all(), 'id', 'name'), ['class' => 'form-control']);
echo Html::beginTag('p', ['style' => 'margin-top: 10px;']);
echo Html::submitButton(Yii::t('app', 'Save'), ['class' => ['btn', 'btn-success'], 'style' => 'float: right']);
echo Html::a(Yii::t('app', 'Cancel'), ['view', 'id' => $model->id], ['class' => ['btn', 'btn-danger']]);
echo Html::endTag('p');
ActiveForm::end();