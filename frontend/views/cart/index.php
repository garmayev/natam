<?php


/**
 * @var $this \yii\web\View
 */

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Client;
use common\models\Order;
use common\models\Product;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

$cart = Yii::$app->cart;
$items = $cart->getItems();

//\yii\bootstrap\BootstrapAsset::register($this);
\frontend\assets\AppAsset::register($this);
$order = new Order();
$products = Product::findAll(["isset" => 0]);
$items = [];
foreach ($products as $product) {
	$items[$product->id] = "{$product->title} ({$product->value})";
}
?>
<script>
    ymaps.ready(init);

    $('.order-block > .btn.blue').on('click', (e) => {
        e.preventDefault();
        let target = $(e.currentTarget);
        if (!target.hasClass('finish')) {
            let closest = target.closest('.order-block');
            closest.removeClass('active').next().addClass('active');
        } else {
            $.ajax({})
            target.closest('#create-order').submit();
        }
    });

    $('[name=\'Client[phone]\']').mask('+7(999)999 9999');

    myMap = undefined;
    myPlacemark = undefined;

    function init() {
        myMap = new ymaps.Map('map', {
            center: [51.76, 107.64],
            zoom: 12
        }, {});

        $('#order-address').suggestions({
            token: '2c9418f4fdb909e7469087c681aac4dd7eca158c',
            type: 'ADDRESS',
            constraints: {
                locations: {region: 'Бурятия'},
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

        // Создание метки.
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
            ymaps.geocode(coords).then(function (res) {
                var firstGeoObject = res.geoObjects.get(0);
                let address = firstGeoObject.getAddressLine();

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
            });
            $('#location-latitude').val(coords[0]);
            $('#location-logintude').val(coords[1]);
        }
    }

</script>
<?php
//ActiveForm::end();
$this->registerCssFile("/css/style.css");


?>
<section class="form" id="form">
    <div class="container-fluid">
        <div class="form_inner">
            <img src="img/journal.png" class="journal" alt="journal">
			<?php $form = ActiveForm::begin(["id" => "create-order", "action" => ["/order/create"], "options" => ["class" => "form_inner"]]); ?>
            <div class="form_content">
				<?php if (!empty($cartItems = $cart->getItems())) { ?>
                    <div id="product_list" class="order-block active form_block">
						<?php
						foreach ($cart->getItems() as $cartItem) {
							$item = $cartItem->getProduct();
							?>
                            <div class="form-group col-lg-6 col-md-6 col-xs-12">
								<?php
								echo Html::dropDownList(
									"Order[product][id][]",
									$cartItem->getId(),
									$items,
									["class" => ["form-control"]]
								);
								?>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-xs-12">
								<?= Html::textInput("Order[product][count][]", $cartItem->getQuantity(), ["class" => "form-control"]); ?>
                            </div>
							<?
						}
						?>
                        <a href="#" class="btn blue">Next</a>
                    </div>
                    <div id="client_info" class="order-block form_block">
                        <div class="form-group col-lg-12 ol-md-12 col-xs-12">
							<?= Html::textInput("Client[name]", "", ["class" => "form-control", "placeholder" => Yii::t("app", "Name")]); ?>
                        </div>
                        <div class="form-group col-lg-12 ol-md-12 col-xs-12">
							<?= Html::textInput("Client[phone]", "", ["class" => "form-control", "placeholder" => Yii::t("app", "Phone")]); ?>
                        </div>
                        <div class="form-group col-lg-12 ol-md-12 col-xs-12">
							<?= Html::textInput("Client[company]", "", ["class" => "form-control", "placeholder" => Yii::t("app", "Company")]); ?>
                        </div>
                        <a href="#" class="btn blue">Next</a>
                    </div>
                    <div id="order_info" class="order-block form_block">
                        <div class="form-group col-lg-12 ol-md-12 col-xs-12">
							<?= \kartik\datetime\DateTimePicker::widget([

								'name' => 'Order[delivery_date]',
								'options' => [
									"id" => "order-delivery_date",
									"placeholder" => Yii::t("app", "Delivery Date"),
								],
								"pluginOptions" => [
									'daysOfWeekDisabled' => [0, 6],
									'hoursDisabled' => '0,1,2,3,4,5,6,7,8,20,21,22,23',
									'minuteStep' => 30,
//				    'minView' => 1,
									'startDate' => date("Y-m-d"),
									'autoclose' => true,
								]
							]) ?>
                        </div>
                        <div class="form-group col-lg-12 ol-md-12 col-xs-12">
							<?= Html::textInput("Order[address]", "", ["id" => "order-address", "class" => "form-control", "placeholder" => Yii::t("app", "Address"), "style" => "margin-bottom: 15px;"]); ?>
                            <input type="hidden" name="Location[title]" id="location-title"">
                            <input type="hidden" name="Location[latitude]" id="location-latitude">
                            <input type="hidden" name="Location[longitude]" id="location-logintude">
							<?php
							// TODO: Разобраться с шириной контейнера карты
							?>
                            <div id="map" style="height: 50vh; width: 100%"></div>
                        </div>
						<?= Html::submitButton("FINISH", ["class" => ["btn", "blue", "finish"]]); ?>
                    </div>
				<?php } else { ?>
                    <label>Корзина пуста</label>
				<?php } ?>
            </div>
			<?php ActiveForm::end(); ?>
        </div>
    </div>
</section>
