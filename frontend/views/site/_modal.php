<?php

use common\models\Client;
use common\models\Order;
use common\models\Product;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

\yii\bootstrap\BootstrapAsset::register($this);
?>

<div id="modal">
    <div class="modal_shadow">&nbsp;</div>

    <div class="container-fluid">
        <div class="form_inner">
			<?= Html::img(Url::to("/img/journal.png"), ["class" => "journal", "alt" => "journal"]) ?>
			<?php
			$client = new Client();
			$order_form = ActiveForm::begin(["action" => Url::to(["order/create"]), "options" => ["class" => ["form_block", "form_order", "active"]]]);
			$order = new Order();
			?>
            <div class="form_content">
                <div class="form_item">
					<?= Html::activeTextInput($client, "name", ["placeholder" => $client->getAttributeLabel("name")]) ?>
					<?= Html::activeTextInput($client, "phone", ["placeholder" => $client->getAttributeLabel("phone")]) ?>
					<?= Html::activeTextInput($client, "company", ["placeholder" => $client->getAttributeLabel("company")]) ?>
					<?= Html::activeTextInput($order, "address", ["placeholder" => $order->getAttributeLabel("address")]) ?>
					<?= Html::activeTextInput($order, "comment", ["placeholder" => $order->getAttributeLabel("comment")]) ?>
                </div>
                <div class="form_item">
					<?php
					echo Html::beginTag("div", ["class" => "form_select"]);
					echo Html::dropDownList("Order[product][id][]", null, ArrayHelper::map(Product::find()->where(["isset" => 0])->all(), "id", "title"), ["prompt" => "Товары"]);
					echo Html::endTag("div");
					?>
                    <a href="#" class="btn blue add_product">
                        Добавить товар
                    </a>
                </div>
                <div class="form_item">
					<?php
					echo Html::beginTag("div");
					echo Html::textInput("Order[product][count][]", null);
					echo Html::endTag("div");r
					?>
                    <div class="form_btn">
                        <div class="form_policy">
                            <!--                                <input type="checkbox" id="form_policy" />-->
                            <label for="form_policy">Нажимая кнопку "Отправть" вы даете свое согласие на обработку
                                персональных данных</label>
                        </div>
                        <button type="submit" class="btn blue">
                            отправить
                        </button>
                    </div>
                </div>
            </div>
			<?php
			ActiveForm::end();
			?>
        </div>
    </div>
</div>
