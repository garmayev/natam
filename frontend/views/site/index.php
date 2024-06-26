<?php

use common\models\Client;
use common\models\Service;
use common\models\Settings;
use common\models\Ticket;
use kartik\datetime\DateTimePicker;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ListView;

/**
 * @var $this View
 * @var $postProvider ActiveDataProvider
 * @var $productProvider ActiveDataProvider
 * @var $serviceProvider ActiveDataProvider
 * @var $categoryProvider ActiveDataProvider
 * @var $menu array
 */

$this->title = Yii::$app->name;
$this->registerJsVar("picker", "");
$this->registerJs(<<< JS
$('.blue.recall').on('click', (e) => {
	$('.form_tab button:first-child').trigger('click');
});
$('.main_inner > .blue').on('click', (e) => {
	$('.form_tab button:last-child').trigger('click');
})
let onChangeMode = function (e) {
    let picker = undefined;
    for ( index in e.delegateTarget) {
        if ((typeof e.delegateTarget[index] === "object") && (e.delegateTarget[index] !== null)) {
            if (e.delegateTarget[index].hasOwnProperty("krajeeDatetimepicker")) {
                picker = e.delegateTarget[index];
                break;
            }
        }
    }
    console.log(e.date.getTimezoneOffset());
    $.ajax({
        url: '/site/change-mode',
        type: 'GET',
        async: false,
        data: {
            date: e.date.getTime() - (3600 * 8)
        },
        success: function (response) {
            let hours = [0, 1, 2, 3, 4, 5, 6, 7, 8, 18, 19, 20, 21, 22, 23, 24];
            for ( let i in response) {
                let d = new Date(i * 1000);
                if (Object.keys(response[i]).length > 4) {
                    hours.push(d.getHours());
                }
            }
            if ( picker !== undefined ) {
                let val = e.date.valueOf() / 1000;
                val = (val - (val % 3600)) * 1000;
                if ( (e.newViewMode === 1) && (e.date.getDay() === 0) ) {
                    for(var i = 0; i < 4; i++) {
                        hours.push(14 + i);
                    }
                }
                picker.datetimepicker.setHoursDisabled( hours.sort().join(",") );
                picker.datetimepicker.setDate( e.date - e.date.getTimezoneOffset() );
            }
        }
    });
}
JS
);
$count = count(Yii::$app->cart->getItems());
if (!empty($success = Yii::$app->session->getFlash("success"))) {
    echo \yii\bootstrap4\Html::tag("div", $success, ["class" => 'alert']);
}
?>
<main>
    <section class="form" id="form">
        <div class="container-fluid">
            <div class="form_inner">
                <img src="img/journal.png" class="journal" alt="journal">
                <div class="form_tab">
                    <button class="">
                        ЗАДАТЬ <br>
                        ВОПРОС
                    </button>
                    <button class="active">ЗАКАЗАТЬ</button>
                </div>
                <?= Html::beginForm(["/ticket/create"], "post", ["class" => ["form_block", "form_submit"]]); ?>
                <!-- <form class="form_block form_submit"> -->
                <input type="hidden" name="Ticket[service_id]" value="0">
                <?php
                $ticket = new Ticket();
                $client = new Client();
                echo Html::beginTag("div", ["class" => "form_content"]);
                echo Html::beginTag("div", ["class" => "form_item"]);
                echo Html::textInput("Client[name]", "", ["placeholder" => "Ваше ФИО", "id" => "ticket-client-name", "required" => true]);
                echo Html::textInput("Client[phone]", "", ["placeholder" => "Ваш номер телефона", "id" => "ticket-client-phone", "required" => true]);
                echo Html::textInput("Client[email]", "", ["placeholder" => "Ваш E-mail", "type" => "email", "id" => "ticket-client-email"]);
                echo Html::textInput("Client[company]", "", ["placeholder" => "Название организации", "id" => "ticket-client-company"]);
                echo Html::endTag("div");
                ?>
                <div class="form_item">
                    <?= Html::textarea("Ticket[comment]", "", ["placeholder" => "Ваш комментарий", "style" => "width: 100%; height: 185px; border-radius: 10px; padding: 18px;"]) ?>
                    <div class="form_btn">
                        <div class="form_policy">
                            <input type="checkbox" id="form_policy">
                            <label for="form_policy">
                                Даю согласие на обработку
                                персональных данных
                            </label>
                        </div>
                        <button type="submit" class="btn blue">
                            отправить
                        </button>
                    </div>
                </div>
                <?php
                echo Html::endTag("div");

                ?>
                <!-- </form> -->
                <?= Html::endForm() ?>
                <?= Html::beginForm(["/order/create"], "post", ["class" => ["form_block", "form_order", "active"], "novalidate" => true, "autocomplete" => "1q2w3e4r5t6y7u8i9o0p"]) ?>
                <div class="order-slide swiper-wrapper">
                    <!--                <form class="form_block form_order active">-->
                    <div class="form_content step swiper-slide" data-index="1">
                        <?php
                        //                        var_dump(Yii::$app->user->identity->client);
                        ?>
                        <div class="form_item"></div>
                        <div class="form_item"></div>
                    </div>
                    <div class="form_content step swiper-slide swiper-no-swiping" data-index="2">
                        <div class="form_item">
                            <input type="text" id="order-client-name" data-required="true" name="Order[client][name]"
                                   placeholder="Ваше ФИО" value="<?= $client->name ?>"
                                   autocomplete="1q2w3e4r5t6y7u8i9o0p">
                            <input type="text" id="order-client-phone" data-required="true" name="Order[client][phone]"
                                   data-inputmask="'mask': '+7(999)999 9999'" placeholder="Ваш номер телефона"
                                   value="<?= $client->phone ?>" autocomplete="1q2w3e4r5t6y7u8i9o0p">
                            <input type="email" id="order-client-company" data-required="false"
                                   name="Order[client][company]"
                                   placeholder="Название организации" value="<?= $client->company ?>"
                                   autocomplete="1q2w3e4r5t6y7u8i9o0p">
                        </div>
                        <div class="form_item">
                            <textarea name="Order[comment]" data-required="false" placeholder="Комментарий" rows="5"
                                      id="order-comment" style="border-radius: 10px; padding: 18px;"
                                      autocomplete="1q2w3e4r5t6y7u8i9o0p"></textarea>
                        </div>
                    </div>
                    <div class="form_content step swiper-slide swiper-no-swiping" data-index="3">
                        <div class="date-address">
                            <div class="form_item">
                                <?=
                                DateTimePicker::widget([
                                    'name' => 'Order[delivery_date]',
                                    'type' => DateTimePicker::TYPE_INPUT,
                                    'options' => [
                                        'class' => ['form'],
                                        'autocomplete' => '1q2w3e4r5t6y7u8i9o0p',
                                        'id' => 'order-delivery_date',
                                        'placeholder' => Yii::t('app', 'Delivery Date'),
                                        'data-required' => "true",
                                    ],
                                    'pluginOptions' => [
                                        'startDate' => date('Y-m-d H:00'),
                                        'daysOfWeekDisabled' => [0],
                                        'minuteStep' => 60,
                                        'autoclose' => true,
                                        'hoursDisabled' => '0,1,2,3,4,5,6,7,8,18,19,20,21,22,23,24',
                                        'minView' => 1,
                                    ],
                                    'pluginEvents' => [
                                        'changeMode' => "onChangeMode",
                                        'changeDate' => "onChangeMode",
                                    ]
                                ]);

                                ?>
                            </div>
                            <div class="form_item">
                                <input type="text" id="order-address" class="form" data-required="true"
                                       name="Order[address]" value="" placeholder="Адрес доставки"
                                       autocomplete="1q2w3e4r5t6y7u8i9o0p">
                                <input type="hidden" name="Order[location][title]" id="location-title" "="">
                                <input type="hidden" name="Order[location][latitude]" id="location-latitude">
                                <input type="hidden" name="Order[location][longitude]" id="location-longintude">
                                <input type="hidden" name="Order[delivery_city]" id="delivery_city" value="1">
                            </div>
                        </div>
                        <div id="map" style=""></div>
                        <div class="form-group">
                            <div class="form-group__elem" style="/*display: flex; flex-direction: row-reverse*/">
                                <label for="delivery_type"
                                       style="font-size: 18px; font-weight: bold; text-transform: uppercase;">Самовывоз</label>
                                <input type="checkbox" name="Order[delivery_type]" id="delivery_type"
                                       style="margin: 0 10px; height: 18px; width: 18px;">
                            </div>
                            <input type="hidden" name="Order[delivery_distance]">
                            <span class="delivery_cost"
                                  style="color: white; font-size: 18px; font-weight: bold;"></span>
                        </div>
                    </div>
                </div>
                <div class="swiper-button-next swiper-button btn blue">Следующий шаг</div>
                <div class="swiper-button-prev swiper-button btn blue">Предыдущий шаг</div>
                <!--                </form>-->
                <?= Html::endForm() ?>
                <?php
                $this->registerJsVar('delivery_cost', Settings::getDeliveryCost());
                ?>
                <script type="module">
                    let myMap, myPlacemark, multiRoute, target, index = 0, base = [51.835488, 107.683083];
                    import Swiper from 'https://unpkg.com/swiper@8/swiper-bundle.esm.browser.min.js'

                    const swiper = new Swiper('.form_order', {
                        direction: 'horizontal',
                        loop: false,
                        noSwiping: true,
                        // Navigation arrows
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        },
                        effect: 'fade',
                        fadeEffect: {
                            crossFade: true
                        },
                        on: {
                            slideNextTransitionStart: function (e) {
                                let current = e.activeIndex;
                                let checked = checkForm(e.slides[e.previousIndex]);
                                if (!checked) {
                                    e.slideTo(e.previousIndex);
                                    e.allowSlideNext = false;
                                }
                                console.log(e);
                                if (current === 2) {
                                    $(".swiper-button-next")
                                        .removeClass("swiper-button-disabled ")
                                        .attr({
                                            "aria-disabled": false,
                                            "data-context": "submit"
                                        });
                                    initMap();
                                }
                            },
                            slidePrevTransitionStart: function (e) {
                                index--;
                            }
                        }
                    });

                    function checkForm(dom) {
                        let fields = $(dom).find("[data-required=true]");
                        let result = true;
                        for (let i = 0; i < fields.length; i++) {
                            if ( $(".product_id").length === 0 ) {
                                return false;
                            } else if ($(fields[i]).val() !== "") {
                                $(fields[i]).addClass("has-success")
                            } else {
                                if ($("#delivery_type").is(":checked")) {
                                    let address_field = $("#order-address");
                                    address_field.addClass('has-success').removeClass('has-error');
                                } else {
                                    $(fields[i]).addClass("has-error")
                                    result = false;
                                }
                            }
                        }
                        return result;
                    }

                    function createPlacemark(coords) {
                        return new ymaps.Placemark(coords, {
                            iconCaption: 'поиск...'
                        }, {
                            preset: 'islands#violetDotIconWithCaption',
                            draggable: true
                        });
                    }

                    function getAddress(coords) {
                        myPlacemark.properties.set('iconCaption', 'поиск...');
                        if (multiRoute !== undefined) {
                            myMap.geoObjects.remove(multiRoute);
                        }
                        multiRoute = new ymaps.multiRouter.MultiRoute({
                            referencePoints: [base, coords],
                            params: {
                                results: 10,
                            }
                        }, {
                            boundsAutoApply: true,
                        });
                        ymaps.geocode(coords).then(function (res) {
                            let firstGeoObject = res.geoObjects.get(0);
                            let address = firstGeoObject.getAddressLine();

                            target = firstGeoObject.properties.get('metaDataProperty');

                            myPlacemark.properties
                                .set({
                                    iconCaption: [
                                        firstGeoObject.getLocalities().length ? firstGeoObject.getLocalities() : firstGeoObject.getAdministrativeAreas(),
                                        firstGeoObject.getThoroughfare() || firstGeoObject.getPremise()
                                    ].filter(Boolean).join(', '),
                                    balloonContent: firstGeoObject.getAddressLine()
                                });
                            $('#order-address').val(address);
                            $('#location-title').val(address);
                            $('#location-latitude').val(coords[0]);
                            $('#location-logintude').val(coords[1]);
                            searchShortest(coords);
                        });
                    }

                    function searchShortest(coords) {
                        multiRoute.model.events.add('requestsuccess', function () {
                            let routes = multiRoute.getRoutes();
                            let shortest = undefined;
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
                            myMap.geoObjects.add(multiRoute);
                            if (target.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.SubAdministrativeArea.Locality.LocalityName !== "Улан-Удэ") {
                                console.log(target.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.SubAdministrativeArea.Locality.LocalityName);
                                let delivery_price = 0;
                                let meters = parseInt(shortest.properties.get("distance").value);
                                $("[name='Order\[delivery_distance\]']").val(meters / 1000);
                                $("#delivery_city").val(0);
                                delivery_price = parseInt(meters / 1000) * delivery_cost;
                                // console.log(delivery_price);
                            }
                            $.ajax({
                                url: '/cart/get-cart',
                                success: (response) => {
                                    let price = 0;
                                    // console.log(response);
                                    for (const [key, value] in response) {
                                        price += response[key].price * parseInt(response[key].quantity);
                                    }
                                    if (delivery_price) {
                                        price += delivery_price;
                                        $(".form-group .delivery_cost").text(`Общая стоимость заказа с доставкой составит: ${price}`);
                                    }
                                }
                            })
                        });
                    }

                    function initMap() {
                        if (myMap === undefined) {
                            myMap = new ymaps.Map('map', {
                                center: [51.76, 107.64],
                                zoom: 12
                            }, {});
                        }
                        $('#order-address').suggestions({
                            token: '2c9418f4fdb909e7469087c681aac4dd7eca158c',
                            type: 'ADDRESS',
                            constraints: {
                                locations: [
                                    {region: 'Бурятия'},
                                    {region: 'Забайкальский'}
                                ],
                            },
                            onSelect: function (suggestion) {
                                ymaps.geocode(suggestion.value, {
                                    results: 1
                                }).then(function (res) {
                                    let placemark = res.geoObjects.get(0),
                                        coords = placemark.geometry.getCoordinates(),
                                        bounds = placemark.properties.get('boundedBy');

                                    if (myPlacemark) {
                                        myPlacemark.geometry.setCoordinates(coords);
                                    } else {
                                        myPlacemark = createPlacemark(coords);
                                        myMap.geoObjects.add(myPlacemark);
                                        myPlacemark.events.add('dragend', function () {
                                            getAddress(myPlacemark.geometry.getCoordinates());
                                        });
                                    }
                                    myMap.setBounds(bounds, {
                                        checkZoomRange: true
                                    });
                                    getAddress(coords);
                                });
                            }
                        });
                        myMap.events.add('click', function (e) {
                            let coords = e.get('coords');

                            if (myPlacemark) {
                                myPlacemark.geometry.setCoordinates(coords);
                            } else {
                                myPlacemark = createPlacemark(coords);
                                myMap.geoObjects.add(myPlacemark);
                                myPlacemark.events.add('dragend', function () {
                                    getAddress(myPlacemark.geometry.getCoordinates());
                                });
                            }
                            getAddress(coords);
                        });
                    }

                    $("#order-delivery_date").removeClass("form-control")
                    $("div.swiper-button-next").on('click', (e) => {
                        index++;
                        let formData = new FormData();
                        // console.log( $(".form_item > .product_id").length !== 0 );
                        if ($('.form_item > .form_select').length !== 0) {
                            if (index === 3) {
                                let checked = checkForm($(`[data-index=${index--}]`));
                                if (checked) {
                                    let products = [];
                                    $(".product_id").each(function () {
                                        products.push(parseInt($(this).val()));
                                    });

                                    let counts = [];
                                    $("input.product_count").each(function () {
                                        counts.push(parseInt($(this).val()));
                                    })

                                    let phone = $("#order-client-phone").val();
                                    phone = phone.replace("/[\(\)\ \+]*/", "");
                                    let data = {
                                        "client_name": $("#order-client-name").val(),
                                        "client_phone": phone,
                                        "dostavka": !$("#delivery_type").val(),
                                        "need_date": $("#order-delivery_date").val(),
                                        "geo_lat": $("#location-latitude").val(),
                                        "geo_lon": $("#location-longitude").val(),
                                        "address": $("#location-title").val(),
                                        "note": $("#order-comment").val(),
                                        "system_id": 1,
                                        "products": JSON.stringify(products),
                                        "kolvoprods": JSON.stringify(counts),
                                    };

                                    $.ajax({
                                        url: "https://www.api.natam03.ru/v1/order/create",
                                        data: JSON.stringify(data),
                                        type: "POST",
                                        headers: {
                                            "Authorization": "Bearer DJjexxZjJRvDfY7o1fZrkNWEXeRp4YxntpyHFHBx",
                                            "Content-Type": "application/json"
                                        },
                                        success: function (response) {
                                            console.log("Check response");
                                            console.log(response); 
                                            if ( response.res ) {
                                                $(".form_order").submit();
                                            }

                                        }
                                    });

                                    e.preventDefault();
                                }
                            }
                        } else {
                            window.location.hash = '#product';
                            swiper.allowClick = false;
                            e.preventDefault();
                        }
                    })
                    $("#delivery_type").on('change', (e) => {
                        if ($(e.currentTarget).is(":checked")) {
                            let date = new Date();
                            $("#order-address").addClass("disabled").attr({disabled: "disabled"});
                            $("#order-delivery_date").addClass("disabled").attr({disabled: "disabled"}).val(`${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()} ${date.getHours()}:${date.getMinutes()}`);
                            myMap.destroy();
                            myMap = undefined;
                        } else {
                            $("#order-address").removeClass("disabled").removeAttr("disabled");
                            $("#order-delivery_date").removeClass("disabled").removeAttr("disabled");
                            initMap();
                        }
                    })
                    $('.product_order > a.btn').on('click', (e) => {
                        console.log($(".product_id").length);
                        if ($(".product_id").length === 0) {
                            e.preventDefault();
                        }
                        if (!$(e.currentTarget).hasClass('disabled')) {
                            let card = $(e.currentTarget).closest('.product_item');
                            let data = `id=${card.find('.cart_product_id').val()}&count=${card.find('.cart_product_count').val()}`;
//                            console.log(data);
                            $.ajax({
                                url: '/cart/add',
                                data: data,
                                type: 'GET',
                                success: (response) => {
                                    window.location.href = '/#form';
                                    rebuild();
                                }
                            })
                        }
                        e.preventDefault();
                    })
                </script>
            </div>
        </div>
    </section>
    <!-- Популярные продукты -->
    <section class="product" id="product">
        <div class="container">
            <div class="product_top">
                <?= Html::tag("h2", "ОФОРМИТЬ ЗАКАЗ", ["class" => "title", "data-aos" => "fade-up"]) ?>
                <?= Html::a("", Url::to("#"), ["class" => "more"]) ?>
            </div>
            <?= ListView::widget([
                "dataProvider" => $categoryProvider,
                "itemView" => "_product",
                "summary" => "",
                "options" => [
                    "tag" => "div",
                    "class" => "product_inner"
                ],
                "itemOptions" => [
                    "tag" => "div",
                    "class" => "product_item",
                    "data-aos" => "zoom-in"
                ],
                "emptyText" => "Пока ничего не добавлено"
            ]) ?>
        </div>
    </section>
    <section class="services">
        <div class="container-fluid">
            <div class="services_inner">
                <?php
                /**
                 * @var $service Service
                 */
                echo Html::tag("div",
                    Html::tag("h2", "ДОПОЛНИТЕЛЬНЫЕ УСЛУГИ", ["class" => "title"]),
                    ["style" => "background: url('/img/services-1.png') no-repeat #fff; text-transform: uppercase;", "class" => "services_item"]
                );
                foreach ($serviceProvider->getModels() as $service) {
                    echo Html::a(
                        Html::tag("h2", $service->title, ["class" => "services_title"]),
                        ["service/view", "id" => $service->id],
                        ["style" => "background: url('$service->thumbs') no-repeat; text-transform: uppercase;", "class" => "services_item"]
                    );
                }
                ?>
            </div>
        </div>
    </section>
    <section class="buy">
        <div class="container-buy">
            <div class="buy_inner">
                <h2 class="title white" data-aos="fade-right">
                    ПОКУПАЕМ Б/У <br/>
                    ГАЗОВЫЕ БАЛЛОНЫ
                </h2>
                <p class="buy_text">
                    В случаях, наличия дефектов, наша компания оказывает услуги комплексного ремонта газовых
                    баллонов, а
                    также реализует новые газовые баллоны по доступным ценам. Также, мы приобретаем б/у баллоны
                    у наших
                    клиентов.
                </p>
                <div class="buy_inner_content" data-aos="fade-right">
                    <a href="tel:71234567890" class="buy_tel"
                    >+7 3012 20 40 56</a
                    >
                    <a href="/#form" class="btn blue recall">
                        <img src="/img/phone.svg" alt="phone"/>
                        ЗАКАЗАТЬ ЗВОНОК
                    </a>
                </div>
                <img src="/img/gaz.png" class="gaz" alt="gas" data-aos="fade-left"/>
            </div>
        </div>
    </section>
    <section class="news">
        <div class="container">
            <div class="news_top">
                <?= Html::tag("h2", "НОВОСТИ", ["class" => "title", "data-aos" => "fade-up"]) ?>
                <?= Html::a("СМОТРЕТЬ ВСЕ", Url::to("/post/index"), ["class" => "more"]) ?>
            </div>
            <?= ListView::widget([
                "dataProvider" => $postProvider,
                "itemView" => "_post",
                "options" => [
                    "tag" => "div",
                    "class" => "news_slider"
                ],
                "itemOptions" => [
                    "tag" => "div",
                    "class" => "news_item"
                ],
                "summary" => "",
            ]) ?>
        </div>
    </section>
