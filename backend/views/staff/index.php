<?php

use common\models\staff\Employee;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\web\View;
use yii\grid\GridView;

/**
 * @var $this View
 * @var $employeeProvider ActiveDataProvider
 */

$this->title = Yii::t('app', 'Staff');
$this->params['breadcrumbs'][] = $this->title;

echo Html::beginTag("p");
echo Html::a(Yii::t("app", "Create Employee"), ['staff/create-employee'], ['class' => ['btn', 'btn-success']]);
echo Html::a(Yii::t("app", "Create State"), ['staff/create-staff'], ['class' => ['btn', 'btn-primary']]);
echo Html::endTag("p");

echo GridView::widget([
	"dataProvider" => $employeeProvider,
	"summary" => "",
	"columns" => [
		"fullname",
		"phone",
		[
			"attribute" => "state_id",
			"label" => Yii::t("app", "State"),
			"format" => "raw",
			"value" => function (Employee $model) {
				return $model->state->title;
			}
		], [
			"class" => ActionColumn::class,
		]
	]
]);