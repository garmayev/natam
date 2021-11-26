<?php

use common\models\Client;
use common\models\Order;
use common\models\Product;
use common\models\Service;
use common\models\Ticket;
use yii\web\View;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;

/**
 * @var $this View
 * @var $postProvider ActiveDataProvider
 * @var $productProvider ActiveDataProvider
 * @var $serviceProvider ActiveDataProvider
 * @var $categoryProvider ActiveDataProvider
 */

$this->title = Yii::$app->name;
?>
<main>
    <!-- Популярные продукты -->
    <section class="product" id="product">
        <div class="container">
            <div class="product_top">
				<?= Html::tag("h2", "ОФОРМИТЬ ЗАКАЗ", ["class" => "title",  "data-aos" => "fade-up"]) ?>
				<?= Html::a("", Url::to("#"), ["class" => "more"]) ?>
            </div>
			<?= ListView::widget([
				"dataProvider" => $categoryProvider,
				"itemView" => "_product",
				"summary" => "",
				"options" => [
					"tag" => "div",
					"class" => "product_inner"
				],
				"itemOptions" => [
					"tag" => "div",
					"class" => "product_item",
					"data-aos" => "zoom-in"
				],
				"emptyText" => "Пока ничего не добавлено"
			]) ?>
        </div>
        <?php
            $this->registerJs("
                $('[name=\'Cart[product_id]\']').on('change', (e) => {
                    let target = $(e.currentTarget);
                    $.ajax('/product/get-product', {
                        data: {id: target.val()},
                        success: (response) => {
                            html = `\${response.price} <span> руб.</span>`;
                            target.next().html(html);
                        }
                    });
                    target.next().next().find($('input')).val(1);
                });", View::POS_LOAD);
        ?>
    </section>
    <section class="services">

        <div class="container-fluid">
            <div class="services_inner">
				<?php
				/**
				 * @var $service Service
				 */
				echo Html::tag("div",
					Html::tag("h2", "ДОПОЛНИТЕЛЬНЫЕ УСЛУГИ", ["class" => "title"]),
					["style" => "background: url('/img/services-1.png') no-repeat #fff; text-transform: uppercase;", "class" => "services_item"]
				);
				foreach ($serviceProvider->getModels() as $service) {
					echo Html::a(
						Html::tag("h2", $service->title, ["class" => "services_title"]),
						["service/view", "id" => $service->id],
						["style" => "background: url('$service->thumbs') no-repeat; text-transform: uppercase;", "class" => "services_item"]
					);
				}
				?>
            </div>
        </div>
    </section>
    <section class="buy">
        <div class="container">
            <div class="buy_inner">
                <h2 class="title white" data-aos="fade-right">
                    ПОКУПАЕМ Б/У <br/>
                    ГАЗОВЫЕ БАЛЛОНЫ
                </h2>
                <p class="buy_text">
                    Это пример текста, создан для того, чтобы было
                    понятно, где будет текст. Это пример текста, создан
                    для того, чтобы было понятно, где будет текст. Это
                    пример текста, создан для того, чтобы было понятно,
                    где будет текст. Это пример текста, создан для того,
                    чтобы было понятно, где будет текст.
                </p>
                <div class="buy_inner_content" data-aos="fade-right">
                    <a href="tel:71234567890" class="buy_tel"
                    >+7 3012 20 40 56</a
                    >
                    <a href="#" class="btn blue recall">
                        <img src="/img/phone.svg" alt="phone"/>
                        ЗАКАЗАТЬ ЗВОНОК
                    </a>
                </div>
                <img src="/img/gaz.png" class="gaz" alt="gas" data-aos="fade-left"/>
            </div>
        </div>
    </section>
    <section class="news">
        <div class="container">
            <div class="news_top">
				<?= Html::tag("h2", "НОВОСТИ", ["class" => "title",  "data-aos" => "fade-up"]) ?>
				<?= Html::a("СМОТРЕТЬ ВСЕ", Url::to("/post/index"), ["class" => "more"]) ?>
            </div>
			<?= ListView::widget([
				"dataProvider" => $postProvider,
				"itemView" => "_post",
				"options" => [
					"tag" => "div",
					"class" => "news_slider"
				],
				"itemOptions" => [
					"tag" => "div",
					"class" => "news_item"
				],
				"summary" => "",
			]) ?>
        </div>
    </section>
</main>
<?php
$this->registerJs("$(() => {
    let myMap, myPlacemark;
    
    $('#cart-pjax a').on('click', (e) => {
        $.modal.open();
    });
})", \yii\web\View::POS_LOAD);
