<?php

use frontend\models\Post;
use yii\helpers\Html;

/**
 * @var $model Post
 */

echo Html::a(Html::img($model->thumbs, ["alt" => $model->title]).Html::tag("div",
		Html::tag("p", $model->title, ["class" => "news_title"]).
		Html::tag("span", Yii::$app->formatter->asDate($model->created_at, "php:d.m.Y"), ["class" => "date"]),
		["class" => "news_content"]
	), ["post/view", "id" => $model->id]);