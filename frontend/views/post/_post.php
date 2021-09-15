<?php

use frontend\models\Post;
use yii\helpers\Html;

/**
 * @var $model Post
 */

$result = Html::beginTag("div", ["class" => "post", "style" => "clear: both; width: 80%; margin: 0 auto;"]).
	Html::tag("div", Html::img($model->thumbs, ["class" => "img-object"]), ["class" => "img"]).
	Html::tag("h3", $model->title).
	Html::tag("div", substr($model->description, 0, 450), ["class" => "text"]).
	Html::endTag("div");

echo Html::a($result, ["/post/view", "id" => $model->id]);