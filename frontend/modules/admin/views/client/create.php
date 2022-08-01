<?php

use common\models\Client;
use yii\web\View;
use yii\helpers\Html;


/**
 * @var $this View
 * @var $model Client|null
 */

$this->title = Yii::t("app", "Create Client");

echo $this->render('_form', [
	'model' => $model
]);