<?php

use common\models\Client;
use common\models\Location;
use common\models\Order;
use common\models\Product;
use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $model Order
 * @var $client Client|null
 */

$index = 0;
$form = ActiveForm::begin(["action" => ($model->isNewRecord) ? Url::to(["/order/create"]) : Url::to(["/order/update", "id" => $model->id])]);
$this->registerJsFile("/js/jquery.maskedinput.min.js", ["depends" => JqueryAsset::class]);
$this->registerJsFile("//api-maps.yandex.ru/2.1/?apikey=0bb42c7c-0a9c-4df9-956a-20d4e56e2b6b&lang=ru_RU");
if (Yii::$app->user->can('employee')) {
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <?= Yii::t("app", "Information about Client") ?>
        </div>
        <div class="panel-body">
            <?php
            echo $form->field($model, "client[name]")->widget(Select2::class, [
                'name' => 'Order[client[name]]',
                'data' => Client::find()->all(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Customer`s name'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ])->label(Yii::t('app', 'Customer`s name'));
            echo $form->field($model, "client[phone]")->label(Yii::t('app', 'Customer`s phone'));
            echo $form->field($model, "client[email]")->label(Yii::t('app', 'Customer`s email'));
            ?>
        </div>
    </div>

    <?php
} else {
    $client = Client::findOne(['phone' => Yii::$app->user->identity->username]);
    echo $form->field($model, 'client_id')->hiddenInput(['value' => $client->id])->label(false);
}
?>
<?php
$allProducts = Product::find()->all();
//$list = ArrayHelper::map(Product::find()->all(), "id", "title");
$list = [];
foreach ($allProducts as $product) {
    $list[$product->id] = "{$product->title} ({$product->value})";
}

$selector = Html::dropDownList(
    "Order[orderProducts][{$index}][product_id]",
    null,
    $list,
    [
        "class" => ["form-control"],
        "style" => "width: 20%",
        "prompt" => "Выберите товар"
    ]
);
$js = "
$(document).on('click', '.panel-heading', function(e){
    var that = $(this);
	if(!that.hasClass('panel-collapsed')) {
		that.parents('.panel').find('.panel-body').slideUp();
		that.addClass('panel-collapsed');
		that.find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
	} else {
		that.parents('.panel').find('.panel-body').slideDown();
		that.removeClass('panel-collapsed');
		that.find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
	}
});
$('[name=\'Client[phone]\']').mask('+7(999)999 9999');
let myMap = myPlacemark = undefined
";
if ($model->location) {
    if ($model->location->latitude) {
        $js .= ",latitude = " . $model->location->latitude . ",longitude = " . $model->location->longitude;
    }
} else {
    $js .= ",latitude = undefined, longitude = undefined";
}
$js .= ";
ymaps.ready(init);
function init() {
    $('#order-address').suggestions({
        token: '2c9418f4fdb909e7469087c681aac4dd7eca158c',
        type: 'ADDRESS',
        constraints: {
            locations: {region: 'Бурятия'},
        },
        onSelect: function (suggestion) {
            console.log(suggestion.value);
            ymaps.geocode(suggestion.value, {
                results: 1
            }).then(function (res) {
                let placemark = res.geoObjects.get(0),
                    coords = placemark.geometry.getCoordinates(),
                    bounds = placemark.properties.get('boundedBy');
                                

                if (myPlacemark !== undefined) {
                    myPlacemark.geometry.setCoordinates(coords);
                } else {
                    myPlacemark = createPlacemark(coords);
                    myMap.geoObjects.add(myPlacemark);
                }
                myMap.setBounds(bounds, {
                    checkZoomRange: true
                });
                getAddress(coords);
            });
        }
    })
    if (longitude && latitude) {
        myMap = new ymaps.Map('map', {
            center: [latitude, longitude],
            zoom: 12
        }, {});
        myPlacemark = createPlacemark([latitude, longitude]);
        myMap.geoObjects.add(myPlacemark);
        getAddress([latitude, longitude]);
    } else { 
        myMap = new ymaps.Map('map', {
            center: [51.76, 107.64],
            zoom: 12
        }, {});
    }
    myMap.events.add('click', function (e) {
        let coords = e.get('coords');

        if (myPlacemark) {
            myPlacemark.geometry.setCoordinates(coords);
        } else {
            myPlacemark = createPlacemark(coords);
            myMap.geoObjects.add(myPlacemark);
        }
        getAddress(coords);
    });

    // Создание метки.
    function createPlacemark(coords) {
        return new ymaps.Placemark(coords, {
            iconCaption: 'поиск...'
        }, {
            preset: 'islands#violetDotIconWithCaption',
            draggable: false
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
            $('#location-latitude').val(coords[0]);
            $('#location-longitude').val(coords[1]);
        });
    }
}
$('.delete-product').on('click', (e) => {
    e.preventDefault();
    $(e.currentTarget).closest('.input-group').remove();
});
$('#order-delivery_type').on('change', (e) => {
    if ($(e.currentTarget).is(':checked')) {
        $('#map').hide();
        $('#order-address').closest('.form-group').hide();
        $('#location-title').closest('.form-group').hide();
        $('#location-latitude').closest('.form-group').hide();
        $('#location-longitude').closest('.form-group').hide();
    } else {
        $('#order-address').closest('.form-group').show();
        $('#location-title').closest('.form-group').show();
        $('#location-latitude').closest('.form-group').show();
        $('#location-longitude').closest('.form-group').show();
        $('#map').show();
    }
})
";
$this->registerJs($js, View::POS_LOAD);
$this->registerCss("
.panel-heading {
    cursor: pointer;
}
.input-group {
    display: flex;
    margin: 15px 0;
}
");
?>
    <div class="panel panel-default">
        <div class="panel-heading" data-toggle="collapse" aria-controls="order-info">
            <?= Yii::t("app", "Information about Order") ?>
        </div>
        <div class="panel-body" id="order-info">
            <?php
            if ($model->isNewRecord || is_null($model->location_id)) {
                $location = new Location();
            } else {
                $location = $model->location;
            }
            echo $form->field($model, "address")->textInput(["placeholder" => Yii::t("app", "Address")]);
            echo $form->field($model, "location[title]")->hiddenInput(['name' => 'Order[location][title]'])->label(false);
            echo $form->field($model, "location[latitude]", ["enableClientValidation" => false])->hiddenInput()->label(false);
            echo $form->field($model, "location[longitude]", ["enableClientValidation" => false])->hiddenInput()->label(false);
            echo Html::tag("div", "", ["id" => "map", "style" => "height: 400px; width: 100%;"]);
            echo $form->field($model, "status")->dropDownList(Order::getStatusList());
            echo DateTimePicker::widget([
                'name' => 'Order[delivery_date]',
                'value' => ($model->delivery_date > 0) ? Yii::$app->formatter->asDatetime($model->delivery_date, 'php:Y-m-d H:i') : "",
                'options' => [
                    "id" => "order-delivery_date",
                    "placeholder" => Yii::t("app", "Delivery Date"),
                ],
                "pluginOptions" => [
                    'daysOfWeekDisabled' => [0, 6],
                    'hoursDisabled' => '0,1,2,3,4,5,6,7,8,20,21,22,23',
                    'minuteStep' => 30,
                    'startDate' => date("Y-m-d"),
                    'autoclose' => true,
                ]
            ]);
            echo $form->field($model, 'delivery_type')->checkbox(['label' => 'Самовывоз']);
            ?>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" data-toggle="collapse" aria-controls="order-controls">
            <?= Yii::t("app", "Order content") ?>
            <span class="nav-pills float-right align-right">
				<a href="#"><span class="fa fa-plus"></span></a>
				<a href="#"><span class="fa fa-trash"></span></a>
			</span>
        </div>
        <div class="panel-body" id="order-controls">
            <?php
            if ($model->isNewRecord) {
                echo Html::beginTag('div', ['class' => ['item']]);
                echo $form->field($model, 'products[order_id]')->hiddenInput(['value' => $model->id])->label(false);
                echo $form
                    ->field($model, 'products[product_id]')
                    ->dropDownList(
                        ArrayHelper::map(Product::find()->all(), 'id', 'title')
                    )
                    ->label(Yii::t('app', 'Select product'));
                echo $form
                    ->field($model, 'products[product_count]')
                    ->textInput()
                    ->label(Yii::t('app', 'Products count'));
                echo Html::endTag('div');
            } else {
                foreach ($model->orderProducts as $orderProduct) {
                    echo Html::beginTag('div', ['class' => ['item']]);
                    echo $form
                        ->field($model, 'products[order_id]')
                        ->hiddenInput([
                            'value' => $model->id
                        ])
                        ->label(false);
                    echo $form
                        ->field($model, 'products[product_id]')
                        ->dropDownList(
                            ArrayHelper::map(Product::find()->all(), 'id', 'title'),
                            ['value' => $orderProduct->product_id]
                        )
                        ->label(Yii::t('app', 'Select product'));
                    echo $form
                        ->field($model, 'products[product_count]')
                        ->textInput(['value' => $orderProduct->product_count])
                        ->label(Yii::t('app', 'Products count'));
                    echo Html::endTag('div');
                }
            }
            ?>
        </div>
    </div>
<?= Html::submitButton() ?>
<?php
ActiveForm::end();