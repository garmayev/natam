<?php

use common\models\Order;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var $models Order[]
 */

$stats = [];
$incomplete = $complete = 0;

$this->registerCss("td.alert {background: yellow}");
$employees = \garmayev\staff\models\Employee::find()->where(["<>", "state_id", 0])->all();
$statuses = Order::getStatus();
?>
<table class="table table-striped table-hover">
    <thead>
    <td>#</td>
	<?php
	foreach ($statuses as $status) {
		echo "<td>{$status}</td><td></td>";
	}
	?>
    </thead>
    <tbody>
	<?php
	foreach ($models as $model) :
		?>
        <tr>
            <td><?= Html::a("#{$model->id}", ["order/view", "id" => $model->id]) ?></td>
			<?php
			$previous = null;
			foreach ($statuses as $index => $status) {
				$event = \common\models\HistoryEvent::find()->where(['field_name' => 'status'])->andWhere(["field_id" => $model->id])->andWhere(["new_value" => $index])->one();
				if ($index === 1) {
					$created = $model->created_at;
					echo "<td>{$model->client->name}<br>(" . Yii::$app->formatter->asDatetime($model->created_at) . ")</td>";
				} else {
					if ($event) {
						$employee = \garmayev\staff\models\Employee::findOne(["user_id" => $event->user_id]);
						if ($created) {
							$ago = Yii::$app->formatter->asTimestamp($event->date) - $created;
							$created = null;
						} else {
							$ago = Yii::$app->formatter->asTimestamp($event->date) - Yii::$app->formatter->asTimestamp($previous->date);
						}
						$interval = \common\models\Settings::getInterval($index - 1);
						if ($interval < $ago) {
							$class = ' class="alert"';
						} else {
							$class = '';
						}
						echo "<td{$class}>" . Yii::$app->formatter->asDuration($ago) . "</td>";
						echo "<td{$class}>{$employee->getFullname()}<br>(" . Yii::$app->formatter->asDatetime($event->date) . ")</td>";
					} else {
						echo "<td></td><td></td>";
					}
				}
				$previous = $event;
			}
			?>
        </tr>
	<?
	endforeach;
	?>
    </tbody>
</table>