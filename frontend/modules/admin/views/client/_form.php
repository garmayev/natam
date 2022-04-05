<?php

use common\models\Client;
use yii\web\View;
use yii\helpers\Html;
use kartik\form\ActiveForm;

/**
 * @var $this View
 * @var $model Client
 */

$form = ActiveForm::begin([]);
echo $form->field($model, 'name');
echo $form->field($model, 'phone');
echo $form->field($model, 'email');
echo $form->field($model, 'company');

echo Html::submitButton(Yii::t("app", "Save"));
echo Html::a(Yii::t("app", "Cancel"), ["/admin/client/index"], ["class" => ['btn', 'btn-danger']]);
ActiveForm::end();