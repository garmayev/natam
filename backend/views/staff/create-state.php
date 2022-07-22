<?php

use common\models\staff\State;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var $this View
 * @var $model State
 */

$form = ActiveForm::begin();
$form->field($model, "title");
$form->field($model, 'salary');
$form->field($model, 'priority');
ActiveForm::end();