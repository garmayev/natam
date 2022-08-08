<?php

use common\models\staff\Employee;
use yii\web\View;
use yii\helpers\Html;
use common\models\TelegramMessage;

/**
 * @var $this View
 * @var $employees Employee[]
 */

$messages = TelegramMessage::find()
    ->where(["order_status" => $employees[0]->state_id])
    ->andWhere(["status" => TelegramMessage::STATUS_CLOSED])
    ->orderBy(["order_id" => SORT_DESC])
    ->all();

$result = [];

foreach ($messages as $message) {
    if ($message->order) {
        $employee = Employee::findOne(["chat_id" => $message->chat_id]);
        if ( $employee ) {
            if (isset($result[$employee->id])) {
                $result[$employee->id]["order"][] = $message->order_id;
            } else {
                $result[$employee->id] = [
                    "order" => [$message->order_id],
                ];
            }
        }
    }
}
?>
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <td><?= Yii::t("app", "Fullname") ?></td>
            <td><?= Yii::t("app", "Distance") ?></td>
            <td><?= Yii::t("app", "Fuel consumption") ?></td>
            <td><?= Yii::t("app", "Mileage") ?></td>
        </tr>
    </thead>
    <tbody>

<?php
//var_dump($result);
foreach ($result as $key => $value) {
    $employee = Employee::findOne($key);
    $orders = \common\models\Order::findAll($value["order"]);
    $distance = 0;
    foreach ($orders as $order) {
//        echo "<p>Order #{$order->id} {$order->delivery_distance}</p>";
        $distance += $order->delivery_distance;
    }
    echo "<tr><td><a href='/admin/staff/view-employee?id={$employee->id}'>{$employee->fullname}</a></td><td>".($distance * 2)."</td>";
    if ( $employee->engine ) {
        echo "<td>{$employee->engine}</td><td>" . ($distance/50) * $employee->engine . "</td></tr>";
    } else {
        echo "<td class='not-set'>".Yii::t("yii", "(not set)")."</td><td class='not-set'>" . Yii::t("yii", "(not set)") . "</td></tr>";
    }
}
?>
    </tbody>
</table>
<small>Все расчеты приблизительные, пробег взят из расчета РАССТОЯНИЕ ДО ЗАКАЗА х 2</small>