<?php

use frontend\models\Service;
use yii\web\View;
use yii\helpers\Html;

/**
 * @var $this View
 * @var $model Service|null
 */

echo Html::tag("div", $model->description);
echo Html::img($model->thumbs);