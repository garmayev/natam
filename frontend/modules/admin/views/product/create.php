<?php

use frontend\models\Product;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $model Product|null
 */

$this->title = Yii::t("app", "New Product");

$form = ActiveForm::begin();
echo $form->field($model, "title")->textInput(["placeholder" => $model->getAttributeLabel("title")])->label(false);
echo $form->field($model, "description")->textarea(["rows" => 4, "placeholder" => $model->getAttributeLabel("description")])->label(false);
echo $form->field($model, "price")->textInput(["placeholder" => $model->getAttributeLabel("price")])->label(false);
echo $form->field($model, "value")->textInput(["placeholder" => $model->getAttributeLabel("value")])->label(false);
echo $form->field($model, "isset")->dropDownList([\Yii::t("natam", "Isset"), \Yii::t("natam", "Empty")])->label(false);
echo $form->field($model, "file")->fileInput();

echo Html::submitButton(Yii::t("app", "Save"), ["class" => ["btn", "btn-success"]]);
echo Html::a(Yii::t("app", "Cancel"), ["/admin/product/index"], ["class" => ["btn", "btn-danger"]]);
ActiveForm::end();