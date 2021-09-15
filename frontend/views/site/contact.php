<?php

use yii\web\View;
use yii\helpers\Html;


/**
 * @var $this View
 */

$this->registerJsFile("//maps.api.2gis.ru/2.0/loader.js?pkg=full", ["depends" => \yii\web\JqueryAsset::class]);
echo Html::tag("div", "", ["id" => "map", "style" => "height: 500px; width: 100%;"]);
$this->registerJs("
    let map;

    DG.then(function () {
        map = DG.map('map', {
            center: [51.835507, 107.68293],
            zoom: 16,
            fullscreenControl: false,
            zoomControl: false
        });
        DG.marker([51.835507, 107.68293]).addTo(map).bindPopup('<p><b>Натам Трейд</b></p><p>&nbsp;</p><p>Компания по продаже технического газа</p><p>г.Улан-Удэ, п.Полигон, 30</p><p>&nbsp;</p><p>Режим работы:</p><p>Пн-Пт с 09:00 до 17:00</p><p>Сб: с 09:00 до 14:00</p><p>Вс: выходной</p>');
    });
", View::POS_LOAD);

//geoФакт.адрес: 670045 г.Улан-Удэ, п.Полигон, 502км База «Разнооптторг-К»
//
//phone Тел.факс:
//8 (3012) 46-74-56,20-40-56