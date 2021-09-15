<?php

use frontend\models\Post;
use yii\web\View;
use yii\helpers\Html;


/**
 * @var $this View
 * @var $model Post|null
 */

echo Html::beginTag("div", ["class" => "media"]);

echo Html::beginTag("div", ["class" => "media-left"]);
echo Html::img($model->thumbs, ["class" => "media-object", "alt" => $model->title]);
echo Html::endTag("div");

echo Html::beginTag("div", ["class" => "media-body"]);

echo Html::tag("h3", $model->title);
echo Html::beginTag("div", ["class" => "description"]);
echo $model->description;
echo Html::endTag("div");
echo Html::tag("hr");
echo Html::beginTag("div", ["class" => "media-bottom"]);
echo Html::tag("p", Html::tag("span", $model->author->username, ["class" => "author"]).Html::tag("span", Yii::$app->formatter->asDatetime($model->created_at, "php:Y m d H:i"), ["class" => "date"]));
echo Html::endTag("div");

echo Html::endTag("div");

echo Html::endTag("div");

$this->registerCss(".author:first-letter {
	text-transform: uppercase;
}
.author {
	display: inline-block;
	padding-right: 10px;
}
.date {
	font-style:italic;
}");