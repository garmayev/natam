<?php

use common\models\Client;
use common\models\HistoryEvent;
use common\models\Order;
use garmayev\staff\models\Employee;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $models Order[]
 */

$stats = [];
$incomplete = $complete = 0;
$this->registerCss("td.alert {background: yellow}");
$employees = \garmayev\staff\models\Employee::find()->where(["<>", "state_id", 0])->all();
$statuses = (new common\models\Order)->getStatus();

if (!isset($_GET['export'])) {
	$form = ActiveForm::begin([
		'method' => 'GET'
	]);
	echo DatePicker::widget([
		'name' => 'startDate',
		'type' => DatePicker::TYPE_INPUT,
		'value' => (isset($_GET['startDate'])) ? Yii::$app->formatter->asDatetime($_GET['startDate'], 'php:d-m-Y') : Yii::$app->formatter->asDatetime(strtotime("-1 year"), 'php:d-m-Y'),
		'pluginOptions' => [
			'autoclose' => true,
			'format' => 'dd-mm-yyyy',
		],
	]);
	echo DatePicker::widget([
		'name' => 'finishDate',
		'type' => DatePicker::TYPE_INPUT,
		'value' => (isset($_GET['finishDate'])) ? Yii::$app->formatter->asDatetime($_GET['finishDate'], 'php:d-m-Y') : Yii::$app->formatter->asDatetime(time(), 'php:d-m-Y'),
		'pluginOptions' => [
			'autoclose' => true,
			'format' => 'dd-mm-yyyy',
		],
	]);
	echo Html::submitButton('Export', ['name' => 'export', 'class' => ['btn', 'btn-success'], 'style' => 'margin: 5px;']);
	echo Html::submitButton("Filter", ['class' => ['btn', 'btn-primary', 'style' => 'margin: 5px;']]);
	ActiveForm::end();
}
$result = [];

foreach ($models as $model) {
	$events = HistoryEvent::find()
		->where(['field_id' => $model->id])
		->andWhere(['field_name' => 'status'])
		->all();
	$result[] = [
		"order_id" => $model->id,
		"status_new" => ['date' => $model->created_at, 'author' => $model->client_id],
		"status_prepare" => 0,
		"status_delivery" => 0,
		"status_complete" => 0,
		"status_cancel" => 0,
		"status_hold" => 0,
	];
	foreach ($events as $event) {
		$index = count($result) - 1;
		switch ($event->new_value) {
			case 2:
				$result[$index]["status_prepare"] = [
					'date' => Yii::$app->formatter->asTimestamp($event->date),
					'author' => $event->user_id,
				];
				break;
			case 3:
				$result[$index]["status_delivery"] = [
					'date' => Yii::$app->formatter->asTimestamp($event->date),
					'author' => $event->user_id,
				];
				break;
			case 4:
				$result[$index]["status_complete"] = [
					'date' => Yii::$app->formatter->asTimestamp($event->date),
					'author' => $event->user_id,
				];
				break;
			case 5:
				$result[$index]["status_cancel"] = [
					'date' => Yii::$app->formatter->asTimestamp($event->date),
					'author' => $event->user_id,
				];
				break;
			case 6:
				$result[$index]["status_hold"] = [
					'date' => Yii::$app->formatter->asTimestamp($event->date),
					'author' => $event->user_id,
				];
				break;
		}
	}
}
function asItem($model, $current, $previous = null)
{
	if (!is_null($previous)) {
		if (isset($model[$current]['author']) && isset($model[$previous]['author'])) {
			$employee = Employee::findOne(['user_id' => intval($model[$current]['author'])]);
			$username = (($employee !== null) && method_exists($employee, 'getFullname')) ? $employee->getFullname() : '';
			return "<p>{$username}</p><p>" . Yii::$app->formatter->asDuration(
					$model[$current]['date'] -
					$model[$previous]['date']) .
				"</p>";
		}
	} else {
		if (isset($model[$current]['author'])) {
			$employee = Employee::findOne(['user_id' => intval($model[$current]['author'])]);
			$username = (($employee !== null) && method_exists($employee, 'getFullname')) ? $employee->getFullname() : '';
			return "<p>{$username}</p><p>" . Yii::$app->formatter->asDatetime($model[$current]['date']) . "</p>";
		}
	}
	return Html::tag('span', Yii::t('yii', '(not set)'), ['class' => 'not-set']);
}

if (count($result)) {
	$dataProvider = new ArrayDataProvider([
		'allModels' => $result
	]);
	$columns = [
		[
			'attribute' => 'order_id',
			'label' => '#',
		],
		[
			'attribute' => 'status_new',
			'label' => $model->getStatus(1),
			'format' => 'html',
			'value' => function ($model) {
				$client = Client::findOne($model['status_new']['author']);
				return "<p>{$client->name}</p><p>" . Yii::$app->formatter->asDatetime($model['status_new']['date']) . "</p>";
			}
		],
		[
			'attribute' => 'status_prepare',
			'label' => $model->getStatus(2),
			'format' => 'html',
			'value' => function ($model) {
				return asItem($model, 'status_prepare', 'status_new');
			}
		],
		[
			'attribute' => 'status_delivery',
			'label' => $model->getStatus(3),
			'format' => 'html',
			'value' => function ($model) {
				return asItem($model, 'status_delivery', 'status_prepare');
			}
		],
		[
			'attribute' => 'status_complete',
			'label' => $model->getStatus(4),
			'format' => 'html',
			'value' => function ($model) {
				return asItem($model, 'status_complete', 'status_prepare');
			}
		],
		[
			'attribute' => 'status_cancel',
			'label' => $model->getStatus(5),
			'format' => 'html',
			'value' => function ($model) {
				return asItem($model, 'status_cancel');
			}
		],
		[
			'attribute' => 'status_cancel',
			'label' => $model->getStatus(5),
			'format' => 'html',
			'value' => function ($model) {
				return asItem($model, 'status_hold');
			}
		],
	];
	echo GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => $columns,
		'summary' => '',
	]);
} else {
	echo Html::tag('span', '(Ничего не найдено)', ['class' => 'not-set']);
}