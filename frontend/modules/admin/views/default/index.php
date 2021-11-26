<?php
/**
 * @var $this View
 */

use yii\web\View;
$this->registerJsFile("//api-maps.yandex.ru/2.1/?apikey=0bb42c7c-0a9c-4df9-956a-20d4e56e2b6b&lang=ru_RU", ["depends" => \yii\web\JqueryAsset::class]);
//$this->registerCssFile("http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.css", ["depends" => \yii\web\JqueryAsset::class]);
//$this->registerJsFile("http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.js", ["depends" => \yii\web\JqueryAsset::class]);
$this->registerJsFile("/js/scout-map.js", ["depends" => \yii\web\JqueryAsset::class]);
?>

<div class="admin-default-index">
    <div id="map" style="height: 800px"></div>
</div>
