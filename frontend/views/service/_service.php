<?php

use frontend\models\Service;
use yii\web\View;
use yii\helpers\Html;

/**
 * @var $this View
 * @var $model Service
 */
echo Html::a(Html::tag("h3", $model->title), ["service/view", "id" => $model->id]);