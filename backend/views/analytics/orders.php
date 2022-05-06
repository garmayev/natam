<?php

use common\models\Order;
use yii\web\View;


/**
 * @var $this View
 * @var $models Order[]
 */

$employees = \garmayev\staff\models\Employee::find()->where(["<>", "state_id", 0])->all();
$stats = [];
foreach ($employees as $employee) {
	$stats[$employee->id] = [
		"employee" => $employee->getFullname(),
		"incomplete" => 0,
		"complete" => 0,
		"total" => 0,
	];
}
$statuses = Order::getStatus();
foreach ($models as $model) :
	$previous = null;
	foreach ($statuses as $index => $status) {
		$event = \common\models\HistoryEvent::find()->where(['field_name' => 'status'])->andWhere(["field_id" => $model->id])->andWhere(["new_value" => $index])->one();
		if ($event) {
			$employee = \garmayev\staff\models\Employee::findOne(["user_id" => $event->user_id]);
			$interval = \common\models\Settings::getInterval($index - 1);
			switch ($event->old_value) {
				case 1:
					$ago = (Yii::$app->formatter->asTimestamp($event->date) - 28800) - $model->created_at;
					$stats[$employee->id][($interval < $ago) ? "incomplete" : "complete"]++;
					$stats[$employee->id]["total"]++;
					break;
				case 2:
					$previous = \common\models\HistoryEvent::find()->where(['field_name' => 'status'])->andWhere(["field_id" => $model->id])->andWhere(["new_value" => $event->old_value])->one();
//					var_dump($model->id);
//					var_dump($previous);
//					var_dump($event->old_value); // die;
//					if ($model->delivery_type !== Order::DELIVERY_SELF) {
						$ago = (Yii::$app->formatter->asTimestamp($event->date) - 28800) - Yii::$app->formatter->asTimestamp($previous->date);
						$stats[$employee->id][($interval < $ago) ? "incomplete" : "complete"]++;
						$stats[$employee->id]["total"]++;
//					} else {
//						var_dump($model->id);
//						var_dump($previous);
//						$ago = (Yii::$app->formatter->asTimestamp($event->date)) - $model->created_at;
//						$stats[$employee->id][($interval < $ago) ? "incomplete" : "complete"]++;
//						$stats[$employee->id]["total"]++;
//					}
					break;
				case 3:
					$ago = (Yii::$app->formatter->asTimestamp($event->date) - 28800) - Yii::$app->formatter->asTimestamp($previous->date);
					$stats[$employee->id][($interval < $ago) ? "incomplete" : "complete"]++;
					$stats[$employee->id]["total"]++;
					break;
				case 4:
					break;
			}
		}
		$previous = $event;
/*		if ($index === 1) {
			$created = $model->created_at;
		} else {
			if ($event) {
				$employee = \garmayev\staff\models\Employee::findOne(["user_id" => $event->user_id]);
				if (isset($employee)) {
					if ($created) {
						$ago = (Yii::$app->formatter->asTimestamp($event->date) - 28800) - $created;
						$created = null;
					} else if (isset($previous)) {
						// $ago = (Yii::$app->formatter->asTimestamp($event->date) - 28800) - Yii::$app->formatter->asTimestamp($previous->date);
					}
					$interval = \common\models\Settings::getInterval($index - 1);
					if ($interval < $ago) {
						$stats[$employee->id]["incomplete"]++;
					} else {
						$stats[$employee->id]["complete"]++;
					}
				}
			}
			$previous = $event;
		}
		$previous = $event; */
	}
endforeach;
?>
<table class="table table-hover table-striped">
    <thead>
    <tr>
        <td>ФИО сотрудника</td>
        <td>Должность</td>
        <td>Выполненных действий</td>
        <td>Просроченных действий</td>
        <td>Всего действий</td>
        <td>Процент выполнения</td>
    </tr>
    </thead>
    <tbody>

	<?php
	foreach ($stats as $index => $stat) {
		$employee = \garmayev\staff\models\Employee::findOne($index);
		if ($stat['total']) {
			$percent = $stat['complete'] / ($stat['total']) * 100;
		} else {
			$percent = 0;
		}
		echo "<tr>
            <td>{$employee->getFullname()}</td>
            <td>{$employee->state->title}</td>
            <td>{$stat['complete']}</td>
            <td>{$stat["incomplete"]}</td>
            <td>{$stat["total"]}</td>
            <td>{$percent}%</td>
            </tr>";
	}
	?>
    </tbody>
</table>
