<?php
use frontend\models\Service;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $model Service|null
 */

$form = ActiveForm::begin();
echo $form->field($model, 'title')->textInput();
echo $form->field($model, 'description')->textarea();
if ( $model->thumbs ) {
	echo Html::img($model->thumbs);
}
echo $form->field($model, 'file')->fileInput();
echo Html::submitButton(Yii::t("app", "Save"), ["class" => ["btn", "btn-success"]]);
ActiveForm::end();