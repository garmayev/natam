<?php

use common\models\Client;
use yii\web\View;
use yii\helpers\Html;


/**
 * @var $this View
 * @var $model Client|null
 */

$this->title = Yii::t("app", "Update Client");

$this->render('_form', [
	'model' => $model
]);
