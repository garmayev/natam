<?php

use common\models\Order;
use common\models\Settings;
use common\models\staff\Employee;
use common\models\staff\State;
use common\models\TelegramMessage;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var $this View
 * @var $models Employee[]
 * @var $orders Order[]
 */

$this->title = Yii::t("app", "Analytics by employee");

$from_date = (isset($_GET['from_date'])) ? Yii::$app->formatter->asTimestamp($_GET['from_date']) : strtotime("-1 month");
$to_date = (isset($_GET['to_date'])) ? Yii::$app->formatter->asTimestamp($_GET['to_date']) : time();
echo Html::beginForm(Url::to(['analytics/employee']), 'get');
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
?>
<table class="table table-striped table-hover">
    <thead>
    <td>ФИО</td>
    <td><?= Yii::t('app', 'Completed') ?></td>
    <td><?= Yii::t('app', 'UnCompleted') ?></td>
    <td><?= Yii::t('app', 'Total') ?></td>
    <td><?= Yii::t('app', 'Percent') ?></td>
    </thead>
    <?php
    foreach ($models as $model) {
        $class = "";
        $total_messages = $completed_messages = $uncompleted_messages = null;
        switch ($model->state_id) {
            case 1:
                $class = "success";
                break;
            case 2:
                $class = "info";
                break;
            case 3:
                $class = "warning";
                break;
        }
        echo Html::beginTag('tr', ["class" => $class]);
        $query = TelegramMessage::find()
            ->where(['updated_by' => $model->user_id])
            ->andWhere(['in', 'order_id', ArrayHelper::getColumn($orders, 'id')])
            ->groupBy(['order_id']);
        $total_messages = (clone $query)
            ->all();
        $completed_messages = (clone $query)
            ->andWhere(['<', '`updated_at` - `created_at`', Settings::getInterval($model->state_id - 1)])
            ->all();
        $uncompleted_messages = (clone $query)
            ->andWhere(['>', '`updated_at` - `created_at`', Settings::getInterval($model->state_id - 1)])
            ->all();
        echo Html::tag('td', $model->getFullname());
        echo Html::tag('td', ($completed_messages) ?
            Html::a(count($completed_messages),
                [
                    "orders-by-status",
                    "from_date" => Yii::$app->formatter->asDate((isset($_GET['from_date'])) ? $_GET['from_date'] : strtotime("-3 month"), "php:d-m-Y"),
                    "to_date" => Yii::$app->formatter->asDate((isset($_GET['to_date'])) ? $_GET['to_date'] : strtotime("+3 month"), "php:d-m-Y"),
                    "employee" => $model->id,
                ]
            ) : 0);
        echo Html::tag('td', ($uncompleted_messages) ?
            Html::a(count($uncompleted_messages),
                [
                    "orders-by-status",
                    "from_date" => Yii::$app->formatter->asDate((isset($_GET['from_date'])) ? $_GET['from_date'] : strtotime("-3 month"), "php:d-m-Y"),
                    "to_date" => Yii::$app->formatter->asDate((isset($_GET['to_date'])) ? $_GET['to_date'] : strtotime("+3 month"), "php:d-m-Y"),
                    "employee" => $model->id,
                    "expired" => "expired"
                ]
            ) : 0);
        echo Html::tag('td', ($total_messages) ? count($total_messages) : 0);
        $percent = ($total_messages && $completed_messages) ? ((count($completed_messages) / count($total_messages)) * 100) : 0;
        echo Html::tag('td', Yii::$app->formatter->asDecimal($percent, 2) . "%", ["style" => "text-align: center"]);
        echo Html::endTag('tr');
    }
    ?>
</table>
<table class="table">
    <tr>
        <td class="success"><?= State::findOne(1)->title ?></td>
        <td class="info"><?= State::findOne(2)->title ?></td>
        <td class="warning"><?= State::findOne(3)->title ?></td>
    </tr>
</table>