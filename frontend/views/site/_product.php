<?php
/**
 * @var $model Product
 */

use frontend\models\Product;
use yii\helpers\Html;

?>
<div class="product_backface">
    <?= Html::tag("span", Html::img("/img/info.svg", ["alt" => "info"]), ["class" => "product_more"]) ?>
    <?= Html::tag("p", $model->description) ?>
</div>
<div class="product_frontface">
	<?= Html::tag("span", Html::img("/img/info.svg", ["alt" => "info"]), ["class" => "product_more"]) ?>
    <?= Html::tag("div", Html::img($model->thumbs, ["alt" => $model->title]), ["class" => "product_img"]) ?>
    <?= Html::tag("p", $model->title, ["class" => "product_item_title"]) ?>
    <?= Html::tag("p", "Объем/Масса: $model->value", ["class" => "product_text"]) ?>
    <?= Html::tag("p", "$model->price".Html::tag("span", "руб."), ["class" => "product_price"]) ?>
    <div class="product_order">
        <div class="product_count">
            <button class="minus">-</button>
            <input type="text" value="1"/>
            <button class="plus">+</button>
        </div>
        <?= Html::a("Заказать", "#", ["class" => ["btn", ($model->isset == 0) ? "blue" : "disabled"]]) ?>
<!--        <a href="#" class="btn blue">заказать</a>-->
    </div>
    <?= ($model->isset == 0) ? Html::tag("span", \Yii::t("natam", "Isset"), ["class" => "product_info"]) : Html::tag("span", \Yii::t("natam", "Empty"), ["class" => "product_info_empty"]) ?>
</div>
