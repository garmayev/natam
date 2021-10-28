<?php

use frontend\models\Vacancy;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $model Vacancy
 */

$form = ActiveForm::begin();
echo $form->field($model, "title")->textInput(["placeholder" => $model->getAttributeLabel("title")])->label(false);
echo $form->field($model, "education")->dropDownList($model->getEducationLabel(), ["prompt" => $model->getAttributeLabel("education")])->label(false);
echo $form->field($model, "experience")->dropDownList($model->getExperienceLabel(), ["prompt" => $model->getAttributeLabel("experience")])->label(false);
echo $form->field($model, "file")->fileInput()->label(false);

echo Html::submitButton(Yii::t("app", "Save"), ["class" => ["btn", "btn-success"]]);
echo Html::a(Yii::t("app", "Cancel"), ["/admin/vacancy/index"], ["class" => ["btn", "btn-danger"]]);
ActiveForm::end();

$this->registerCss(".btn {margin-right: 15px;}");