<?php

use frontend\models\Client;
use frontend\models\Order;
use frontend\models\Product;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $model Order
 * @var $client Client|null
 */
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
$selector = Html::dropDownList("Order[product][id][]", null, ArrayHelper::map(Product::find()->all(), "id", "title"), ["class" => ["form-control", "col-lg-2", "col-md-3", "col-xs-4"], "style" => "width: 300px", "prompt" => "Выберите товар"]);
$this->registerJs("
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
    $(`<div class=\"input-group\">{$selector}<input type=\"text\" name=\"Order[product][count][]\" class=\"form-control col-lg-10 col-md-9 col-xs-8\" placeholder=\"Введите количество\"></div>`).insertBefore($(e.currentTarget).parent());
    console.log($(e.currentTarget).parent());
})
", View::POS_LOAD);
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
			echo $form->field($model, "address")->textInput();
			echo $form->field($model, "status")->dropDownList($model->getStatus());
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
				$result = "<tr><td><div class='input-group'>" .
					Html::dropDownList("Order[product][id][]", $product->id, ArrayHelper::map(Product::find()->all(), "id", "title"), ["class" => ["form-control"], "style" => "width: 300px", "prompt" => "Выберите товар"]) .
					"<input type='text' name='Order[product][count][]' class='form-control col-lg-10 col-md-9 col-xs-8' value='{$model->getCount($product->id)}' placeholder='Введите количество'></div></td></tr>";
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