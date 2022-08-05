<?php

use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ListView;

/**
 * @var $this View
 * @var $postProvider ActiveDataProvider
 */

echo Html::a("New Post", ["post/create"], ["class" => ["btn", "btn-success"]]);

echo ListView::widget([
	"dataProvider" => $postProvider,
	"summary" => "",
	"itemView" => "_post",
]);