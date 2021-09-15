<?php

use frontend\models\Vacancy;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var $this View
 * @var $vacancyProvider ActiveDataProvider
 * @var $model Vacancy
 */

echo Html::a(Yii::t("app", "Append Product"), ["/admin/vacancy/create"], ["class" => ["btn", "btn-success"]]);

echo GridView::widget([
	"dataProvider" => $vacancyProvider,
	"summary" => "",
	"formatter" => ["class" => \yii\i18n\Formatter::className(),'nullDisplay' => ''],
	"columns" => [
		"title",
		[
			"attribute" => "education",
			"content" => function ($model) {
				return $model->getEducationLabel($model->education);
			}
		],[
			"attribute" => "experience",
			"content" => function ($model) {
				return $model->getExperienceLabel($model->experience);
			}
		], [
			"attribute" => "status",
			"content" => function ($model) {
				return $model->getStatusLabel($model->status);
			}
		], [
			"class" => \yii\grid\ActionColumn::className(),
			"template" => "{update} {delete}",
		]
	],
]);