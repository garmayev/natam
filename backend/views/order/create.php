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
$index = 0;
$form = ActiveForm::begin(["action" => \yii\helpers\Url::to(["/order/update", "id" => $model->id])]);
$this->registerJsFile("/js/jquery.maskedinput.min.js", ["depends" => \yii\web\JqueryAsset::class]);
$this->registerJsFile("//cdn.jsdelivr.net/npm/suggestions-jquery@21.8.0/dist/js/jquery.suggestions.min.js", ["depends" => \yii\web\JqueryAsset::class]);
$this->registerJsFile("//api-maps.yandex.ru/2.1/?apikey=0bb42c7c-0a9c-4df9-956a-20d4e56e2b6b&lang=ru_RU");
$this->registerCssFile("//cdn.jsdelivr.net/npm/suggestions-jquery@21.6.0/dist/css/suggestions.min.css");
?>
    <div class="panel">
        <div class="panel-heading panel-default">
			<?= Yii::t("app", "Information about Client") ?>
        </div>
		<?php
		echo Html::beginTag("div", ["class" => ["panel-body"]]);
		if (is_null($model->client)) {
			$model->client = new Client();
		}
		echo $form->field($model->client, "name");
		echo $form->field($model->client, "phone");
		echo Html::endTag("div");
		?>
    </div>

<?php
//$allProducts = Product::find()->all();
$list = ArrayHelper::map(Product::find()->all(), "id", "title");
//foreach ($allProducts as $product) {
//	$list[$product->id] = "{$product->title} ({$product->value})";
//}

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
        <div class="panel-heading" data-toggle="collapse" aria-controls="order-info">
			<?= Yii::t("app", "Information about Order") ?>
        </div>
        <div class="panel-body" id="order-info">
			<?php
			if (!is_null($model->location)) {
				$model->location = new \common\models\Location();
			}
			echo $form->field($model, "address")->textInput(["placeholder" => Yii::t("app", "Address")]);
			echo $form->field($model->location, "title")->hiddenInput()->label(false);
			echo $form->field($model->location, "latitude")->hiddenInput()->label(false);
			echo $form->field($model->location, "longitude")->hiddenInput()->label(false);

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
        <div class="panel-heading" data-toggle="collapse" aria-controls="order-controls">
			<?= Yii::t("app", "Order content") ?>
        </div>
        <div class="panel-body" id="order-controls">
			<?php
            $op = new \common\models\OrderProduct();
			\wbraganca\dynamicform\DynamicFormWidget::begin([
				'widgetContainer' => 'dynamicform_wrapper',
				'widgetBody' => '.container-items',
				'widgetItem' => '.item',
                'model' => ($model->orderProducts) ? $model->orderProducts[0] : new \common\models\OrderProduct(),
                'formId' => "w0",
                'insertButton' => '.add-product',
                'deleteButton' => '.delete',
				"min" => 1,
				'formFields' => [
					'product_id',
					'product_count',
                    'order_id',
				],
			]);
			// necessary for update action.
            echo Html::beginTag("div", ["class" => "container-items"]);
//            var_dump($model->orderProducts); die;
            foreach ($model->orderProducts as $index => $orderProduct) {
	            echo Html::beginTag("div", ["class" => "item"]);
	            echo Html::activeHiddenInput($orderProduct, "[{$index}]order_id", ["value" => $model->id]);
	            echo $form->field($orderProduct, "[{$index}]product_id")->dropDownList($list, [
		            "class" => ["form-control"],
		            "style" => "width: 20%",
		            "prompt" => "Выберите товар"
	            ])->label(false);
	            echo $form->field($orderProduct, "[{$index}]product_count")->textInput(["placeholder" => "Введите количество"])->label(false);
	            echo Html::endTag("div");
            }
			echo Html::endTag("div");
			echo Html::tag("p", Html::a(Yii::t("app", "Append Product"), "#", ["class" => ["btn", "btn-success", "add-product"]]));
            \wbraganca\dynamicform\DynamicFormWidget::end();
			echo Html::submitButton(Yii::t("app", "Save"), ["class" => ["btn", "btn-primary"], "style" => "margin-right: 10px;"]);
			echo Html::a(Yii::t("app", "Cancel"), Yii::$app->request->referrer, ["class" => ["btn", "btn-danger"]]);
			?>
        </div>
    </div>
<?php
ActiveForm::end();