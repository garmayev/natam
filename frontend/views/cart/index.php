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

\yii\bootstrap\BootstrapAsset::register($this);
\frontend\assets\AppAsset::register($this);
$order = new Order();
$products = Product::findAll(["isset" => 0]);
$items = [];
foreach ($products as $product) {
	$items[$product->id] = "{$product->title} ({$product->value})";
}
$form = ActiveForm::begin(["id" => "create-order", "action" => ["/order/create"]]);
//$this->registerJsFile('/js/cart-index-script.js', ["depends" => \yii\web\JqueryAsset::className()]);
?>
<?php if (!empty($cartItems = $cart->getItems())): ?>
    <div id="product_list" class="order-block active">
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
    <div id="client_info" class="order-block">
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
    <div id="order_info" class="order-block">
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
			<?= Html::textInput("Order[address]", "", ["id" => "order-address", "class" => "form-control", "placeholder" => Yii::t("app", "Address")]); ?>
            <input type="hidden" name="Location[title]" id="location-title">
            <input type="hidden" name="Location[latitude]" id="location-latitude">
            <input type="hidden" name="Location[longitude]" id="location-logintude">
            <div id="map" style="height: 50vh;"></div>
        </div>
		<?= Html::submitButton("FINISH", ["class" => ["btn", "blue", "finish"]]) ?>
    </div>
<?php else: ?>
    <h3>Корзина пуста</h3>
<?php endif; ?>
<?php
ActiveForm::end();
$this->registerJsFile("/js/cart-index-script.js", ["depends" => \yii\web\JqueryAsset::class]);
