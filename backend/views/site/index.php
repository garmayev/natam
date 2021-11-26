<?php

/* @var $this yii\web\View */

$this->title = 'Панель управления';
$spik = new \backend\models\SPIK();
$this->registerCssFile("http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.css");
$this->registerJsFile("http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.js");
$this->registerJsFile("js/scout-map.js");
echo \yii\helpers\Html::tag("div", "<div id='mapPlaceHolder' style='height: 600px;'></div>", ["id" => "mapDiv"]);
?>
