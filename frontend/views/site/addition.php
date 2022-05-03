<?php

use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;



/**
 * @var $this View
 * @var $productProvider ActiveDataProvider
 */

$this->registerCssFile('/css/style.css');
$this->registerCssFile('/css/programmer.css');
//var_dump(count($productProvider->getModels()));
?>
<main>
<section class="product">
	<div class="container">
<?php
echo Html::beginTag('div', ['class' => 'product_inner']);
foreach ($productProvider->getModels() as $model) {
	echo Html::beginTag('div', ['class' => 'product_item', 'data-aos' => 'zoom-in', 'data-key' => $model->id]);
	?>
	<div class="product_backface">
		<?= Html::tag("span", Html::img("/img/info.svg", ["alt" => "info"]), ["class" => "product_more"]) ?>
		<?= Html::tag("p", $model->title) ?>
	</div>
	<div class="product_frontface">
		<?= Html::tag("span", Html::img("/img/info.svg", ["alt" => "info"]), ["class" => "product_more"]) ?>
		<?= Html::tag("div", Html::img($model->thumbs, ["alt" => $model->title]), ["class" => "product_img"]) ?>
		<?= Html::tag("p", $model->title, ["class" => "product_item_title"]) ?>
        <?= Html::tag("p", "{$model->price}".Html::tag("span", " руб."), ["class" => "product_price"]) ?>
        <?= Html::hiddenInput("Cart[product_id]", $model->id, ["class" => "cart_product_id"]) ?>
        <div class="product_order">
            <div class="product_count">
                <button class="minus" <?= "disabled=disabled" ?>>-</button>
                <input type="text" value="1" name="Cart[product_count]" class="cart_product_count" <?= "disabled=disabled" ?>/>
                <button class="plus" <?= "disabled=disabled" ?>>+</button>
            </div>
			<?= Html::a("Заказать", "#", ["class" => ["btn", "blue"]]) ?>
        </div>
	</div>

<?php
	echo Html::endTag('div');
}
echo Html::endTag('div');
$this->registerJs('
    AOS.init();
    $(".product_more, .product_img > img").on("click", function () {
        $(this).closest(".product_item").toggleClass("active");
    });
                        $(".product_order > a.btn").on("click", (e) => {
                            if (!$(e.currentTarget).hasClass("disabled")) {
				$(e.currentTarget).addClass("disabled");
                                let card = $(e.currentTarget).closest(".product_item");
                                data = `id=${card.find(".cart_product_id").val()}&count=${card.find(".cart_product_count").val()}`
                                $.ajax({
                                    url: "/cart/add",
                                    data: data,
                                    type: "GET",
                                    success: (response) => {
                                        rebuild();
                                    }
                                })
                            }
                            e.preventDefault();
                        });
    $(".product_more, .product_img > img").on("click", function () {
        $(this).closest(".product_item").toggleClass("active");
    });

');
?>
	</div>
</section>
</main>