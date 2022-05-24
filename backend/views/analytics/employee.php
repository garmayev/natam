<?php

use common\models\Client;
use common\models\HistoryEvent;
use common\models\Order;
use common\models\Settings;
use common\models\TelegramMessage;
use garmayev\staff\models\Employee;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $models Employee[]
 * @var $orders Order[]
 */
$from_date = (isset($_GET['from_date'])) ? Yii::$app->formatter->asTimestamp($_GET['from_date']) : Yii::$app->params['startDate'];
$to_date = (isset($_GET['to_date'])) ? Yii::$app->formatter->asTimestamp($_GET['to_date']) : time();
echo Html::beginForm(Url::to(['analytics/employee']), 'get');
echo DatePicker::widget([
	'name' => 'from_date',
	'value' => Yii::$app->formatter->asDate($from_date, 'php:Y-m-d'),
	'type' => DatePicker::TYPE_RANGE,
	'name2' => 'to_date',
	'value2' => Yii::$app->formatter->asDate($to_date, 'php:Y-m-d'),
	'separator' => '<i class="fas fa-arrows-h"></i>',
	'pluginOptions' => [
		'autoclose' => true,
		'format' => 'yyyy-mm-dd'
	]
]);
echo Html::submitButton('Export', ['class' => ['btn', 'btn-success'], 'name' => 'export', 'value' => 'export']);
echo Html::submitButton('Filter', ['class' => ['btn', 'btn-primary'], 'name' => 'filter', 'value' => 'filter']);
echo Html::endForm();
?>
<table class="table table-striped">
    <thead>
        <td>ФИО</td>
        <td><?= Yii::t('app', 'Completed') ?></td>
        <td><?= Yii::t('app', 'UnCompleted') ?></td>
        <td><?= Yii::t('app', 'Total') ?></td>
        <td><?= Yii::t('app', 'Percent') ?></td>
    </thead>
	<?php
	foreach ($models as $model) {
		echo Html::beginTag('tr');
        $query = TelegramMessage::find()
	        ->where(['updated_by' => $model->user_id])
            ->andWhere(['in', 'order_id', ArrayHelper::getColumn($orders, 'id')]);
		$total_messages = (clone $query)
			->all();
		$completed_messages = (clone $query)
            ->andWhere(['<', '`updated_at` - `created_at`', Settings::getInterval($model->state_id - 1)])
			->all();
		$uncompleted_messages = (clone $query)
            ->andWhere(['>', '`updated_at` - `created_at`', Settings::getInterval($model->state_id - 1)])
			->all();
		echo Html::tag('td', $model->getFullname());
		echo Html::tag('td', ($completed_messages) ? count($completed_messages) : 0);
		echo Html::tag('td', ($uncompleted_messages) ? count($uncompleted_messages) : 0);
		echo Html::tag('td', ($total_messages) ? count($total_messages) : 0);
		echo Html::tag('td', ($total_messages && $completed_messages) ? ((count($completed_messages) / count($total_messages)) * 100) . "%" : '0%');
		echo Html::endTag('tr');
	}
	?>
</table>