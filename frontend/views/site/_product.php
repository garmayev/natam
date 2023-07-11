<?php
/**
 * @var $model Category
 */

use common\models\Category;
use common\models\Product;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>
<div class="product_backface">
    <?= Html::tag("span", Html::img("/img/info.svg", ["alt" => "info"]), ["class" => "product_more"]) ?>
    <?= Html::tag("p", $model->content) ?>
</div>
<?php
$products = $model->products;
$any = false;
$items = [];
foreach ( $products as $product ) {
    $items[$product->id] = "{$product->title} ({$product->value})";
    if ( $product->isset == 0 ) {
        $any = true;
    }
}
?>
<div class="product_frontface">
	<?= Html::tag("span", Html::img("/img/info.svg", ["alt" => "info"]), ["class" => "product_more"]) ?>
    <?= Html::tag("div", Html::img($model->thumbs, ["alt" => $model->title]), ["class" => "product_img"]) ?>
    <?= Html::tag("p", $model->title, ["class" => "product_item_title"]) ?>
    <?php
        if ( $any ) {
            if ( count($items) > 1 ) {
	        echo Html::dropDownList("Cart[product_id]", 0, $items, ["style" => "width: 92%; padding: 5px; margin: 10px 0;", "class" => "cart_product_id"]);
            } else {
	        echo Html::dropDownList("Cart[product_id]", 0, $items, ["style" => "width: 92%; padding: 5px; margin: 10px 0;", "class" => "cart_product_id", "disabled" => "disabled"]);
            }
            echo Html::tag("p", "{$model->products[0]->price}".Html::tag("span", " руб."), ["class" => "product_price"]);
        } else {
            echo Html::dropDownList("Cart[product_id]", 0, $items, ["style" => "width: 92%; padding: 5px; margin: 10px 0;", "class" => "cart_product_id", "disabled" => "disabled"]);
            echo Html::tag("span", \Yii::t("natam", "Empty"), ["class" => "product_info_empty"]);
        }
    ?>
    <div class="product_order">
        <div class="product_count">
            <button class="minus">-</button>
            <input type="text" value="1" name="Cart[product_count]" class="cart_product_count" />
            <button class="plus">+</button>
        </div>
        <?= Html::a("Заказать", "#", ["class" => ["btn", "blue"]]) ?>
    </div>
</div>
