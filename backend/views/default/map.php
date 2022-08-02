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

$orders = Order::find()->all();
$points = [];
$icons = [
    Order::STATUS_NEW => "islands#blueCircleDotIconWithCaption",
    Order::STATUS_PREPARED => "islands#greenCircleDotIconWithCaptio",
    Order::STATUS_DELIVERY => "islands#yellowCircleDotIconWithCaptio",
    Order::STATUS_COMPLETE => "islands#blackCircleDotIconWithCaption",
    Order::STATUS_CANCEL => "islands#redCircleDotIconWithCaption",
];

//foreach (Location::find()->all() as $key => $location) {
//    if ($location) {
//        $points[] = [
//            "type" => "Feature",
//            "id" => $location->id,
//            "geometry" => [
//                "type" => "Point",
//                "coordinates" => [$location->latitude, $location->longitude],
//            ],
//            "options" => [
//                "preset" => $icons[1],
//            ]
//        ];
//    }
//}
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
                "selected" => false
            ], [
                "index" => Order::STATUS_CANCEL,
                "title" => Order::getStatusList()[Order::STATUS_CANCEL],
                "selected" => false
            ],
        ],
        "label" => Yii::t("app", "Status"),
    ],
    "container" => '#map',
    "field" => null,
    "points" => [
        "type" => "FeatureCollection",
        "features" => $points
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