<?php

use common\models\Client;
use yii\bootstrap\ActiveForm;
use yii\web\View;
use yii\helpers\Html;


/**
 * @var $this View
 * @var $model Client|null
 */

$form = ActiveForm::begin();
echo $form->field($model, 'name');
echo $form->field($model, 'phone');
echo $form->field($model, 'email');
echo $form->field($model, 'company');
echo Html::submitButton(Yii::t('app', 'Save'), ['class'=> ['btn', 'btn-success']]);
ActiveForm::end();