<?php

use common\models\staff\State;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $model State
 */

$form = ActiveForm::begin();
echo $form->field($model, "title");
echo $form->field($model, 'salary');
echo $form->field($model, 'priority');
echo Html::submitButton(Yii::t('app', 'Save'), ['class' => ['btn', 'btn-success']]);
ActiveForm::end();