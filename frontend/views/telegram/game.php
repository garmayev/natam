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

/**
 * @param $category_id
 * @return array
 */
function products($category_id)
{
    $result = [];
    $products = Product::find()->where(['category_id' => $category_id])->all();
    foreach ($products as $product) $result[$product->id] = "$product->title ($product->value)";
    return $result;
}

?>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="https://natam03.ru/favicon.png"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/webapp.css">
    <style>

    </style>
</head>
<body style="overflow: auto" class="bg-white">
<div class="main">
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true" style="overflow: auto">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Создать заказ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <label for="delivery_date">Выберите дату и время доставки</label>
                    <input type="date" class="form-control" id="delivery_date" name="Order[delivery_date]">
                </p>
                <p>
                    <input type="checkbox" id="order-delivery_type">
                    <label for="order-delivery_type">Самовывоз</label>
                </p>
                <div id="order-location-field">
                    <label for="location-title">Введите адрес доставки или выберите точку на карте</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="Order[location][title]" id="location-title">
                        <div class="input-group-append">
                            <button class="input-group-text" id="btnGroupAddon">
                                <span class="fas fa-map-marker-alt"></span>
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="Order[location][latitude]" id="location-latitude">
                    <input type="hidden" name="Order[location][longitude]" id="location-longitude">
                    <input type="hidden" name="Order[point_id]" id="point_id">
                    <input type="hidden" name="Order[distance]" id="distance">
                    <div id="map" style="min-height: 200px; display: none;" class="mt-2"></div>
                </div>
                <p class="pt-2">
                    <span class="btn btn-primary append"
                          data-target="#append-product"
                          data-toggle="modal">Добавить продукт</span>
                </p>
                <div class="cart_table" id="cart_table"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">Отмена
                </button>
                <button type="button" class="btn btn-primary"
                        id="create-order">Заказать
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="append-product" tabindex="-1" role="dialog" aria-labelledby="appendProductLabel"
     aria-hidden="true" style="overflow: auto">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавить продукт</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t("app", "Close") ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <label for="category_id">Выберите категорию</label>
                    <?= Html::dropDownList("category_id", null, ArrayHelper::map(Category::find()->all(), 'id', 'title'), ['class' => 'form-control', 'id' => 'category_id']) ?>
                </p>
                <p>
                    <label for="product_id">Выберите продукт</label>
                    <?= Html::dropDownList("product_id", null, products(1), ['class' => 'form-control', 'id' => 'product_id']) ?>
                </p>
                <p>
                    <label for="product_count">Введите количество</label>
                    <input type="number" class="form-control" name="product_count" id="product_count">
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">Отмена
                </button>
                <button type="button" class="btn btn-primary append-product">Добавить</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="clone-order" tabindex="-1" role="dialog" aria-labelledby="cloneOrderLabel"
     aria-hidden="true" style="overflow: auto">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Повторить заказ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t("app", "Close") ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <label for="clone-delivery_at">Выберите дату и время доставки</label>
                    <input type="date" class="form-control" id="clone-delivery_at">
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">Отмена
                </button>
                <button type="button" class="btn btn-primary clone-order">Сохранить</button>
            </div>
        </div>
    </div>
</div>
<script src="//telegram.org/js/telegram-web-app.js"></script>
<script src="//kit.fontawesome.com/aa23fe1476.js"></script>
<script src="//api-maps.yandex.ru/2.1/?apikey=886ddc0b-177a-47eb-8b68-2e3a58cf27d9&lang=ru_RU"
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
    import {Order, User, Cart} from "/js/telegram-game.js";

    ymaps.ready(() => {

        let tg = window.Telegram.WebApp, map = undefined, placemark = undefined, stores = undefined,
            closest = undefined, multiRoute = undefined,
            cart = new Cart(document.querySelector("#cart_table")),
            suggestView = new ymaps.SuggestView("location-title", {
                boundedBy: [[29, 100], [31, 120]],
            });

        window.user = new User(document.querySelector("body > .main"), tg.initDataUnsafe.user ? tg.initDataUnsafe.user.id : "443353023")
        tg.expand();

        suggestView.events.add("select", (e) => {
            ymaps.geocode(e.originalEvent.item.value, {
                results: 1,
            }).then((response) => {
                let firstGeoObject = response.geoObjects.get(0),
                    coords = firstGeoObject.geometry.getCoordinates();
                if (map) {
                    if (placemark) {
                        placemark.geometry.setCoordinates(coords)
                    } else {
                        placemark = new ymaps.Placemark(coords);
                        map.geoObjects.add(placemark);
                    }
                    map.setCenter(coords);
                }
                getAddress(coords);
            })
        })

        window.user.on(User.EVENT_LOGGED, function (e) {
            let orders = Order.get(this);
            Order.buildTable(document.querySelector("body > .main"), orders);
        }.bind(window.user));

        $("#category_id").on("change", (e) => {
            $.ajax("/api/product/by-category?category_id=" + $(e.currentTarget).val()).then(response => {
                let target = $("#product_id");
                target.html("");
                for (const element of response) {
                    target.append(`<option value='${element.id}'>${element.title} (${element.value})</option>`);
                }
            })
        })

        $(".append-product").on('click', (e) => {
            if (cart.append({
                category_id: $("#category_id").val(),
                product_id: $("#product_id").val(),
                product_count: $("#product_count").val(),
                product_balloon: $("#product_balloon").val()
            })) {
                $("#append-product").modal("hide");
            }
        })

        $("#order-delivery_type").on("change", (e) => {
            if ($(e.currentTarget).is(":checked")) {
                map.destroy();
                map = undefined;
                placemark = undefined;

                $("#location-title").val("");
                $("#location-latitude").val("");
                $("#location-longitude").val("");
                $("#order-location-field").hide();
            } else {
                init();
                $("#order-location-field").show();
            }
        })

        $('.modal').css("overflow", "auto");

        $('#exampleModal').on('show.bs.modal', () => {
            if (map === undefined && !$("#order-delivery_type").is(":checked")) {
                init();
            }
        })

        $("#create-order").on("click", (e) => {

            let order = {
                Order: {
                    client_id: undefined,
                    chat_id: tg.initDataUnsafe.user ? tg.initDataUnsafe.user.id : "443353023",
                    delivery_date: $("#delivery_date").val(),
                    delivery_type: $("#order-delivery_type").is(":checked") ? 0 : 1,
                    location: {
                        title: $("#location-title").val(),
                        latitude: $("#location-latitude").val(),
                        longitude: $("#location-longitude").val(),
                    },
                    point_id: $("#point_id").val(),
                    delivery_distance: $("#distance").val(),
                    products: cart.selected,
                    telegram: 1,
                }
            }
            console.log(order);
            $.ajax({
                url: "/api/order/create",
                data: order,
                method: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Authorization", "Bearer " + user._token);
                },
            }).then(response => {
                if (response.ok) {
                    $("#exampleModal").modal("hide");
                    let orders = Order.get(user);
                    Order.buildTable(document.querySelector("body > .main"), orders);
                }
            })
        })

        $(".clone-order").on("click", (e) => {
            let delivery_at = "#clone-delivery_at",
                order_id = $(delivery_at).attr("data-key");
            $.ajax({
                url: `/api/order/clone?id=${order_id}`,
                data: {"delivery_date": $(delivery_at).val()},
                method: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Authorization", "Bearer " + user._token);
                },
            }).then(response => {
                if (response.ok) {
                    $("#clone-order").modal("hide");
                    let orders = Order.get(user);
                    Order.buildTable(document.querySelector("body > .main"), orders);
                }
            })
        })

        $("#btnGroupAddon").on("click", (e) => {
            let mapContainer = $("#map");
            if (mapContainer.is(":visible")) {
                mapContainer.hide();
            } else {
                mapContainer.show();
            }
        })

        function getAddress(coords) {
            placemark.properties.set('iconCaption', 'поиск...');
            closest = stores.getClosestTo(coords);
            ymaps.geocode(coords).then(function (res) {
                let firstGeoObject = res.geoObjects.get(0),
                    address = firstGeoObject.getAddressLine();
                if (multiRoute !== undefined) {
                    map.geoObjects.remove(map.multiRoute);

                }
                multiRoute = new ymaps.multiRouter.MultiRoute({
                    referencePoints: [
                        coords,
                        stores.getClosestTo(coords)
                    ],
                    params: {
                        results: 1
                    }
                }, {
                    boundsAutoApply: true
                });
                multiRoute.model.events.add('requestsuccess', function () {
                    let routes = multiRoute.getRoutes(), distance = undefined, shortest = undefined;
                    routes.each((route) => {
                        if (typeof shortest === "undefined") {
                            shortest = route;
                        } else {
                            if (shortest.properties.get("distance").value > route.properties.get("distance").value) {
                                shortest = route;
                            }
                        }
                    })
                    if (typeof shortest !== "undefined") {
                        multiRoute.setActiveRoute(shortest);
                    }
                    distance = parseInt(shortest.properties.get("distance").value);

                    $("#location-title").val(address);
                    $("#location-latitude").val(coords[0]);
                    $("#location-longitude").val(coords[1]);
                    $("#point_id").val(closest.properties._data["data-key"]);
                    $("#distance").val(distance);
                    placemark.properties
                        .set({
                            iconCaption: address,
                        });
                });
            });
        }

        function init() {
            map = new ymaps.Map("map", {
                center: [51.835501, 107.683123],
                zoom: 17,
                controls: [],
            });
            let features = [], points = [{
                id: 1, title: "Натам-Трейд", location: {
                    latitude: 51.835501,
                    longitude: 107.683123
                }
            }];
            for (let i = 0; i < points.length; i++) {
                let point = points[i];
                features.push({
                    type: 'Feature',
                    options: {
                        iconLayout: 'default#image',
                        iconImageHref: '/img/icons/placemark.svg',
                        iconImageSize: [260, 205],
                        iconImageOffset: [-25, -155],
                        hideIconOnBalloonOpen: false,
                        balloonOffset: [270, -110],
                    },
                    properties: {
                        'data-key': point.id,
                        city: point.city,
                        balloonContent: `<div class="map-ballon" style="overflow-wrap: break-word;"><p class="text-ballon">${point.title}</p><p class="number-ballon"><span class="number-span-ballon">8${point.code}</span> ${point.phone}</p></div>`,
                    },
                    geometry: {
                        type: 'Point',
                        coordinates: [point.location.latitude, point.location.longitude]
                    }
                })
            }
            stores = ymaps.geoQuery({
                type: 'FeatureCollection',
                features: features
            }).addToMap(map);
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

        window.user.init();
    })
</script>

</body>
</html>