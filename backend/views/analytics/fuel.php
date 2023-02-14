<?php

use common\models\Order;
use common\models\staff\Employee;
use common\models\TelegramMessage;
use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var $this View
 * @var $employees Employee[]
 */

$this->title = Yii::t("app", "Fuel consumption");

$from_date = (isset($_GET['from_date'])) ? Yii::$app->formatter->asTimestamp($_GET['from_date']) : Yii::$app->params['startDate'];
$to_date = (isset($_GET['to_date'])) ? Yii::$app->formatter->asTimestamp($_GET['to_date']) : time();
echo Html::beginForm(Url::to(['analytics/fuel']), 'get');
echo Html::beginTag("p");
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
echo Html::endTag("p");
echo Html::submitButton(Yii::t("app", 'Export'), ['class' => ['btn', 'btn-success'], 'name' => 'export', 'value' => 'export', 'style' => "margin-right: 10px;"]);
echo Html::submitButton(Yii::t("app", 'Filter'), ['class' => ['btn', 'btn-primary'], 'name' => 'filter', 'value' => 'filter']);
echo Html::endForm();

$messages = TelegramMessage::find()
    ->where(["order_status" => $employees[0]->state_id])
    ->andWhere(["status" => TelegramMessage::STATUS_CLOSED])
    ->orderBy(["order_id" => SORT_DESC])
    ->all();

$result = [];

//var_dump($from_date, $to_date);

foreach ($messages as $message) {
    if ($message->order && ($message->order->created_at > $from_date) && ($message->order->created_at < $to_date)) {
        $employee = Employee::findOne(["chat_id" => $message->chat_id]);
        if ($employee) {
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
        <td><?= Yii::t("app", "Fuel consumption by 100 km") ?></td>
        <td><?= Yii::t("app", "Fuel consumption") ?></td>
    </tr>
    </thead>
    <tbody>

    <?php
    $totalDistance = 0;
    $fuel = 0;
    //var_dump($result);
    foreach ($result as $key => $value) {
        $employee = Employee::findOne($key);
        $orders = Order::findAll($value["order"]);
        $distance = 0;
        foreach ($orders as $order) {
//        echo "<p>Order #{$order->id} {$order->delivery_distance}</p>";
            $distance += $order->delivery_distance;
        }
        echo "<tr><td><a href='/admin/staff/view-employee?id={$employee->id}'>{$employee->fullname}</a></td><td>" . Yii::$app->formatter->asLength($distance * 2000) . "</td>";
        $totalDistance += ($distance * 2000);
        if ($employee->engine) {
            $fuel += ($distance / 50) * $employee->engine;
            echo "<td>{$employee->engine}</td><td>" . ($distance / 50) * $employee->engine . " л</td></tr>";
        } else {
            echo "<td class='not-set'>" . Yii::t("yii", "(not set)") . "</td><td class='not-set'>" . Yii::t("yii", "(not set)") . "</td></tr>";
        }
    }
    ?>
    </tbody>
    <tfoot style="font-weight: bold">
    <td>Итого</td>
    <td><?= Yii::$app->formatter->asLength($totalDistance) ?></td>
    <td></td>
    <td><?= $fuel ?> л</td>
    </tfoot>
</table>
<small>Все расчеты приблизительные, пробег взят из расчета РАССТОЯНИЕ ДО ЗАКАЗА х 2</small>