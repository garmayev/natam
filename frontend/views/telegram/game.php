<?php

use common\models\Category;
use common\models\Product;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;


/**
 * @var $this View
 * @var $models ActiveDataProvider
 */

?>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="https://natam03.ru/favicon.png"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/webapp.css">
</head>
<body style="overflow: auto" class="bg-white">
<div class="main">
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?= Yii::t("app", "Create Order") ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t("app", "Close") ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <input type="datetime-local" class="form-control" name="Order[delivery_at]">
                </p>
                <p>
                    <input type="text" class="form-control" name="Order[location][title]" id="location-title">
                    <input type="hidden" name="Order[location][latitude]" id="location-latitude">
                    <input type="hidden" name="Order[location][longitude]" id="location-longitude">
                </p>
                <div id="map" style="min-height: 200px"></div>
                <p class="pt-2">
                    <span class="btn btn-primary append"
                          data-target="#append-product"
                          data-toggle="modal"><?= Yii::t("app", "Append Product") ?></span>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal"><?= Yii::t("app", "Cancel") ?></button>
                <button type="button" class="btn btn-primary create-order"><?= Yii::t("app", "Save") ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="append-product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?= Yii::t("app", "Append Product") ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t("app", "Close") ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <?= Html::dropDownList("category_id", null, ArrayHelper::map(Category::find()->all(), 'id', 'title'), ['class' => 'form-control']) ?>
                </p>
                <p>
                    <?= Html::dropDownList("category_id", null, ArrayHelper::map(Product::find()->where(['category_id' => 1])->all(), 'id', 'title'), ['class' => 'form-control']) ?>
                </p>
                <p>
                    <input type="number" class="form-control">
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal"><?= Yii::t("app", "Cancel") ?></button>
                <button type="button" class="btn btn-primary append-product"><?= Yii::t("app", "Save") ?></button>
            </div>
        </div>
    </div>
</div>
<script src="//telegram.org/js/telegram-web-app.js"></script>
<script src="//kit.fontawesome.com/aa23fe1476.js"></script>
<script src="//api-maps.yandex.ru/2.1/?apikey=0bb42c7c-0a9c-4df9-956a-20d4e56e2b6b&lang=ru_RU"
        type="text/javascript"></script>
<script src="//code.jquery.com/jquery-3.6.3.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
<script type="module" src="/js/telegram-game.js"></script>
<script type="module">
    import {Order, User} from "/js/telegram-game.js";

    document.addEventListener("DOMContentLoaded", () => {
        let tg = window.Telegram.WebApp, map = undefined, placemark = undefined,
            user = new User(document.querySelector("body > .container-fluid"), tg.initDataUnsafe.user ? tg.initDataUnsafe.user.id : "443353023");

        tg.expand();

        user.on(User.EVENT_LOGGED, function (e) {
            let orders = Order.get(this);
            Order.buildTable(document.querySelector("body > .main"), orders);
            // console.log(orders);
        }.bind(user));

        $(".append-product").on('click', () => {
            
        })

        function getAddress(coords) {
            placemark.properties.set('iconCaption', 'поиск...');
            ymaps.geocode(coords).then(function (res) {
                let firstGeoObject = res.geoObjects.get(0),
                    address = firstGeoObject.getAddressLine();
                $("#location-title").val(address);
                $("#location-latitude").val(coords[0]);
                $("#location-longitude").val(coords[1]);
                placemark.properties
                    .set({
                        // Формируем строку с данными об объекте.
                        iconCaption: address,
                        // В качестве контента балуна задаем строку с адресом объекта.
                        // balloonContent: firstGeoObject.getAddressLine()
                    });
            });
        }

        $('#exampleModal').on('show.bs.modal', () => {
            if (map === undefined) {
                map = new ymaps.Map("map", {
                    center: [51.835501, 107.683123],
                    zoom: 17,
                    controls: [],
                });
                map.events.add("click", (e) => {
                    let coords = e.get("coords")
                    if (placemark) {
                        placemark.geometry.setCoordinates(coords)
                    } else {
                        placemark = new ymaps.Placemark(coords);
                        map.geoObjects.add(placemark);
                    }
                    getAddress(coords);
                })
            }
            console.log("Modal open");
        })

        user.init();
    })
</script>

</body>
</html>