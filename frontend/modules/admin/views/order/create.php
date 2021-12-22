<?php

use common\models\Client;
use common\models\Order;
use common\models\Product;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $model Order
 * @var $client Client|null
 */

$this->title = Yii::t("app", "New Order");

$form = ActiveForm::begin();
?>
    <div class="panel">
        <div class="panel-heading panel-default">
			<?= Yii::t("app", "Information about Client") ?>
        </div>
		<?php
		echo Html::beginTag("div", ["class" => ["panel-body"]]);
		if (is_null($client)) {
			if (is_null($model->client)) {
				$client = new Client();
			} else {
				$client = Client::findOne($model->client_id);
			}
		}
		echo Html::tag("label", $client->getAttributeLabel("name"), ["class" => "control-label", "for" => "client-name"]);
		echo Html::tag("p", Html::activeTextInput($client, "name", ["class" => "form-control", "placeholder" => $client->getAttributeLabel("name")]));
		echo Html::tag("label", $client->getAttributeLabel("phone"), ["class" => "control-label", "for" => "client-phone"]);
		echo Html::tag("p", Html::activeTextInput($client, "phone", ["class" => "form-control", "placeholder" => $client->getAttributeLabel("phone")]));
		echo Html::endTag("div");
		?>
    </div>

<?php
$allProducts = Product::find()->all();
$list = [];
foreach ($allProducts as $product) {
	$list[$product->id] = "{$product->title} ({$product->value})";
}

$selector = Html::dropDownList("Order[product][id][]", null, $list, ["class" => ["form-control"], "style" => "width: 20%", "prompt" => "Выберите товар"]);
$this->registerJsFile("/js/jquery.maskedinput.min.js", ["depends" => \yii\web\JqueryAsset::class]);
$this->registerJsFile("//cdn.jsdelivr.net/npm/suggestions-jquery@21.8.0/dist/js/jquery.suggestions.min.js", ["depends" => \yii\web\JqueryAsset::class]);
$this->registerJsFile("//api-maps.yandex.ru/2.1/?apikey=0bb42c7c-0a9c-4df9-956a-20d4e56e2b6b&lang=ru_RU");
$this->registerCssFile("//cdn.jsdelivr.net/npm/suggestions-jquery@21.6.0/dist/css/suggestions.min.css");
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
$('.add-product').on('click', (e) => {
    e.preventDefault();
    $(`<div class=\"input-group\">{$selector}<input type=\"text\" name=\"Order[product][count][]\" class=\"form-control\" style='width: 70%;' placeholder=\"Введите количество\"><a class='delete-product input-group-btn fa fa-trash' style='width: 10%; text-align: center; padding-top: 12px;'></a></div>`).insertBefore($(e.currentTarget).parent());
});
$('[name=\'Client[phone]\']').mask('+7(999)999 9999');
let myMap = myPlacemark = undefined
";
if ( $model->location ) {
    if ( $model->location->latitude ) {
        $js .= ",latitude = ".$model->location->latitude.",longitude = ".$model->location->longitude;
    }
} else {
    $js .= ",latitude = undefined, longitude = undefined";
}
$js .= ";
ymaps.ready(() => {
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
        console.log(longitude);
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
        });
        $('#location-latitude').val(coords[0]);
        $('#location-logintude').val(coords[1]);
    }
});
$('.delete-product').on('click', (e) => {
    e.preventDefault();
    $(e.currentTarget).closest('.input-group').remove();
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
        <div class="panel-heading">
			<?= Yii::t("app", "Information about Order") ?>
        </div>
        <div class="panel-body">
			<?php
            if ( isset( $model->location ) ) {
	            echo $form->field($model, "address")->textInput(["placeholder" => Yii::t("app", "Address")]);
	            echo $form->field($model->location, "title")->hiddenInput()->label(false);
                echo $form->field($model->location, "latitude")->hiddenInput()->label(false);
	            echo $form->field($model->location, "longitude")->hiddenInput()->label(false);
            } else {
	            echo $form->field($model, "address")->textInput(["placeholder" => Yii::t("app", "Address")]);
            }

//			echo Html::textInput("Order[address]", $model->address, ["id" => "order-address", "class" => "form-control", "placeholder" => Yii::t("app", "Address"), "style" => "margin-bottom: 15px;"]);
            echo Html::tag("div", "", ["id" => "map", "style" => "height: 400px; width: 100%;"]);
			echo $form->field($model, "status")->dropDownList($model->getStatus());
            echo \kartik\datetime\DateTimePicker::widget([

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
            ])
			?>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
			<?= Yii::t("app", "Order content") ?>
        </div>
        <div class="panel-body">
			<?php
			foreach ($model->products as $product) {
				$result = "<div class='input-group'>" .
					Html::dropDownList("Order[product][id][]", $product->id, $list, ["class" => ["form-control"], "style" => "width: 20%", "prompt" => "Выберите товар"]) .
					"<input type='text' name='Order[product][count][]' class='form-control' style='width: 70%;' value='{$model->getCount($product->id)}' placeholder='Введите количество'>".
                    "<a class='delete-product input-group-btn fa fa-trash' style='width: 10%; text-align: center; padding-top: 12px;'></a></div>";
				echo $result;
			}
			echo Html::tag("p", Html::a(Yii::t("app", "Append Product"), "#", ["class" => ["btn", "btn-success", "add-product"]]));
			echo Html::submitButton(Yii::t("app", "Save"), ["class" => ["btn", "btn-primary"], "style" => "margin-right: 10px;"]);
			echo Html::a(Yii::t("app", "Cancel"), Yii::$app->request->referrer, ["class" => ["btn", "btn-danger"]]);
			?>
        </div>
    </div>
<?php
ActiveForm::end();