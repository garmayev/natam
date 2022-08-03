<?php

use common\models\Location;
use common\models\Order;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var $orders Order[]
 */

$points = [];
function orders()
{
    $icons = [
        Order::STATUS_NEW => "islands#blueCircleDotIconWithCaption",
        Order::STATUS_PREPARED => "islands#greenCircleDotIconWithCaptio",
        Order::STATUS_DELIVERY => "islands#yellowCircleDotIconWithCaptio",
        Order::STATUS_COMPLETE => "islands#blackCircleDotIconWithCaption",
        Order::STATUS_CANCEL => "islands#redCircleDotIconWithCaption",
    ];

    $used = $points = [];
    foreach (Order::find()->all() as $key => $order) {
        if ($order->location) {
            $points[] = [
                "type" => "Feature",
                "id" => $order->location->id,
                "geometry" => [
                    "type" => "Point",
                    "coordinates" => [$order->location->latitude, $order->location->longitude],
                ],
                "options" => [
                    "id" => $order->id,
                    "status" => $order->status,
                    "preset" => $icons[$order->status],
                ],
                "properties" => [
                    "balloonContent" => Yii::t("app", "Loading..."),
                    "clusterCaption" => Yii::t("app", "Order #{id}", ['id' => $order->id])
                ]
            ];
            $used[] = $order->location->title;
        }
    }
    return $points;
}
//Yii::error($points);

$options = [
    "map" => [
        "center" => [51.805823003568, 107.62057385448],
        "zoom" => 15,
        "controls" => [],
    ],
    "filter" => [
        "items" => [
            [
                "index" => Order::STATUS_NEW,
                "title" => Order::getStatusList()[Order::STATUS_NEW],
                "selected" => true
            ], [
                "index" => Order::STATUS_PREPARED,
                "title" => Order::getStatusList()[Order::STATUS_PREPARED],
                "selected" => true
            ], [
                "index" => Order::STATUS_DELIVERY,
                "title" => Order::getStatusList()[Order::STATUS_DELIVERY],
                "selected" => true
            ], [
                "index" => Order::STATUS_COMPLETE,
                "title" => Order::getStatusList()[Order::STATUS_COMPLETE],
                "selected" => true
            ], [
                "index" => Order::STATUS_CANCEL,
                "title" => Order::getStatusList()[Order::STATUS_CANCEL],
                "selected" => true
            ],
        ],
        "label" => Yii::t("app", "Status"),
    ],
    "container" => '#map',
    "points" => [
        "type" => "FeatureCollection",
        "features" => orders()
    ],
];
$this->registerJsVar("options", $options);
$this->registerJsVar("statusList", Order::getStatusList());
$this->registerJsFile('/admin/js/map.js');
$this->registerJs(<<< JS
    ymaps.ready().then((e) => {
        let m = new Map(options);
        m.init();
    })
JS
    , View::POS_LOAD);

echo Html::tag('div', '', ['id' => 'map', 'style' => 'height: 450px; width: 100%;']);