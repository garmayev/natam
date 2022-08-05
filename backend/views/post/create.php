<?php

use common\models\Post;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $model Post
 */

$form = ActiveForm::begin();
echo $form->field($model, "title")->textInput(["placeholder" => Yii::t("app", "Title")])->label(false);
echo $form->field($model, "description")->textarea(["rows" => 12, "placeholder" => Yii::t("app", "Description")])->label(false);
echo $form->field($model, "file")->fileInput(["placeholder" => Yii::t("app", "File")])->label(false);
echo Html::submitButton(Yii::t("app", "Save"), ["class" => ["btn", "btn-success"]]);
echo Html::a(Yii::t("app", "Cancel"), ["post/index"], ["class" => ["btn", "btn-danger"]]);
ActiveForm::end();