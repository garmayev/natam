<?php

use yii\web\View;

/**
 * @var $this View
 * @var $cars array
 */

$this->title = 'Панель управления';
//$this->registerCssFile("http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.css");
//$this->registerJsFile("http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.js");
//$this->registerJsFile("js/scout-map.js");
var_dump($cars);
$this->registerJsVar('units', $cars, View::POS_READY);

echo \yii\helpers\Html::tag("div", "<div id='mapPlaceHolder'></div>", ["id" => "mapDiv"]);
