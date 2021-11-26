<?php

use dektrium\user\models\User;
use common\models\Staff;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var $this View
 * @var $model User
 */

$form = ActiveForm::begin();

echo $form->field($model, "username");
echo $form->field($model, "email");
echo $form->field($model, "password");

if ( !is_null($model->staff) ) {
	$staff = $model->staff;
} else {
	$staff = new Staff();
}

echo $form->field($staff, "state")->dropDownList(Staff::stateLabels());
echo $form->field($staff, "phone");

echo Html::submitButton(Yii::t("app", "Save"), ["class" => ["btn", "btn-success"]]);
ActiveForm::end();