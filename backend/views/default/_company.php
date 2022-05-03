<?php

use common\models\Client;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $client Client
 * @var $form ActiveForm
 */
$this->registerCssFile("//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css");

$this->registerJsFile("//code.jquery.com/ui/1.13.1/jquery-ui.js", ["depends" => \yii\web\JqueryAsset::class]);
$this->registerJsFile("/admin/js/organizations.js", ["depends" => \yii\web\JqueryAsset::class]);

echo $form->field($client, "company");
