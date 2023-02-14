<?php

use common\models\Order;
use kartik\date\DatePicker;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ListView;


/**
 * @var $this View
 * @var $models Order[]
 */

$this->title = Yii::t("app", "Analytics by orders");

$from_date = (isset($_GET['from_date'])) ? Yii::$app->formatter->asTimestamp($_GET['from_date']) : strtotime("-1 month");
$to_date = (isset($_GET['to_date'])) ? Yii::$app->formatter->asTimestamp($_GET['to_date']) : time();
echo Html::beginForm(Url::to(['analytics/orders']), 'get');
echo Html::beginTag("p", ["style" => "margin: 0 10px"]);
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
echo Html::submitButton(Yii::t('app', 'Filter'), ['class' => ['btn', 'btn-primary'], 'name' => 'filter', 'value' => 'filter']);
echo Html::endForm();

echo ListView::widget([
    'dataProvider' => new ArrayDataProvider([
        "allModels" => $models,
        "pagination" => [
            "pageSize" => 25,
        ]
    ]),
    'itemView' => '_item',
    'summary' => '',
]);