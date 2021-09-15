<?php

use frontend\models\Service;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var $model Service
 * @var $this View
 */

echo Html::beginTag("a", ["style" => "background: center/cover url('$model->thumbs')", "href" => Url::to(["service/view", "id" => $model->id])]);
echo Html::tag("h2", $model->title, ["class" => "service_title"]);
echo Html::endTag("a");
