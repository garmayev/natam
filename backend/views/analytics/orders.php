<?php

use common\models\Order;
use yii\web\View;


/**
 * @var $this View
 * @var $models Order[]
 */

$employees = \garmayev\staff\models\Employee::find()->where(["<>", "state_id", 0])->all();
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
		if ($index === 1) {
			$created = $model->created_at;
		} else {
			if ($event) {
				$employee = \garmayev\staff\models\Employee::findOne(["user_id" => $event->user_id]);
				if (isset($employee)) {
					if ($created) {
						$ago = Yii::$app->formatter->asTimestamp($event->date) - $created;
						$created = null;
					} else {
						$ago = Yii::$app->formatter->asTimestamp($event->date) - Yii::$app->formatter->asTimestamp($previous->date);
					}
					$interval = \common\models\Settings::getInterval($index - 1);
					if ($interval < $ago) {
						$stats[$employee->id]["incomplete"]++;
					} else {
						$stats[$employee->id]["complete"]++;
					}
					$stats[$employee->id]["total"]++;
				}
			}
		}
		$previous = $event;
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
