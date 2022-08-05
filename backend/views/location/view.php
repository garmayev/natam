<?php

use common\models\Location;
use common\models\Order;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var $model Location
 */

$this->registerJsVar("point", $model->attributes);

echo Html::tag("div", "", ["class" => "container","id" => "map", "style" => "height: 50vh"]);

echo GridView::widget([
	"dataProvider" => new ArrayDataProvider([
		"allModels" => $model->orders
	]),
	"summary" => "",
	"columns" => [
		[
			"attribute" => "id",
			"format" => "html",
			"value" => function (Order $model) {
				return Html::a("#{$model->id}", ["order/view", "id" => $model->id]);
			}
		], [
			"attribute" => "client.name",
			"format" => "html",
			"value" => function (Order $model) {
				return Html::a($model->client->name, ["client/view", "id" => $model->client_id]);
			}
		], [
			"attribute" => "client.phone",
			"format" => "html",
			"value" => function (Order $model) {
				return Html::a($model->client->phone, "tel:+{$model->client->phone}");
			}
		], [
			"attribute" => "comment",
			"format" => "html",
			"value" => function (Order $model) {
				if ( isset($model->comment) && $model->comment != '' ) {
					return Html::tag('div', $model->comment);
				}
				return Html::tag("p", Yii::t("yii", "(not set)"), ["class" => "not-set"]);
			}
		], [
			"attribute" => "status",
			"value" => function (Order $model) {
				return $model->getStatusName();
			}
		], [
			"attribute" => "created_at",
			"value" => function (Order $model) {
				return Yii::$app->formatter->asDatetime($model->created_at, "php:d F Y H:i");
			}
		], [
			"attribute" => "delivery_date",
			"value" => function (Order $model) {
				return Yii::$app->formatter->asDatetime($model->delivery_date, "php:d F Y H:i");
			}
		]
	]
]);
$this->registerJsFile("//api-maps.yandex.ru/2.1/?apikey=0bb42c7c-0a9c-4df9-956a-20d4e56e2b6b&lang=ru_RU", ["depends" => \yii\web\JqueryAsset::class]);
$this->registerJsFile("/admin/js/tracker.js", ["depends" => \yii\web\JqueryAsset::class]);
$this->registerJs(<<< JS
$(() => {
    console.log(point);
    ymaps.ready().done(() => {
        let map = new ymaps.Map("map", {
            center: [point.latitude, point.longitude],
            zoom: 16,
            controls: []
        });
        let placemark = new ymaps.Placemark([point.latitude, point.longitude], {
            iconCaption: point.title
        });
        map.geoObjects.add(placemark);
    })
})
JS);