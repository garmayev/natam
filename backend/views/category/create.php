<?php

use common\models\Category;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var $this View
 * @var $model Category
 */

\common\widgets\Alert::widget();

$form = ActiveForm::begin(["method" => "post"]);

echo $form->field($model, "title")->textInput(["placeholder" => Yii::t("app", "Title")])->label(false);
echo $form->field($model, "content")->textarea(["placeholder" => Yii::t("app", "Content")])->label(false);
echo $form->field($model, "image")->fileInput(["placeholder" => Yii::t("app", "Image")])->label(false);
echo Html::submitButton(Yii::t("app", "Save"), ["class" => ["btn", "btn-success"], "style" => "margin-right: 10px;"]);
echo Html::a(Yii::t("app", "Cancel"), ["category/index"], ["class" => ["btn", "btn-danger"]]);
ActiveForm::end();