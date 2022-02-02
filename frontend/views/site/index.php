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
    <section class="form" id="form">
        <div class="container-fluid">
            <div class="form_inner">
                <img src="img/journal.png" class="journal" alt="journal">
                <div class="form_tab">
                    <button class="">
                        ОСТАВИТЬ <br>
                        ЗАЯВКУ
                    </button>
                    <button class="active">ЗАКАЗАТЬ</button>
                </div>
                <form class="form_block form_submit">
                    <?php
                        $ticket = new Ticket();
                        $client = new Client();
                        echo Html::beginTag("div", ["class" => "form_content"]);
                            echo Html::beginTag("div", ["class" => "form_item"]);
                                echo Html::textInput("Client[name]", "", ["placeholder" => "Ваше ФИО"]);
                                echo Html::textInput("Client[phone]", "", ["placeholder" => "+ 7 ( ____ ) - ___ - __ - __"]);
                                echo Html::textInput("Client[email]", "", ["placeholder" => "Ваш E-mail", "type" => "email"]);
                                echo Html::textInput("Client[company]", "", ["placeholder" => "Название организации"]);
                            echo Html::endTag("div");
                    ?>
                            <div class="form_item">
                                <?= Html::textarea("Ticket[comment]", "", ["placeholder" => "Ваш комментарий", "style" => "width: 100%; height: 185px; border-radius: 10px; padding: 18px;"]) ?>
                            <div class="form_btn">
                                <div class="form_policy">
                                    <input type="checkbox" id="form_policy">
                                    <label for="form_policy">
                                        Даю согласие на обработку
                                        персональных данных
                                    </label>
                                </div>
                                <button type="submit" class="btn blue">
                                    отправить
                                </button>
                            </div>
                        </div>
                    <?php
                        echo Html::endTag("div");

                    ?>
                </form>
                <form class="form_block form_order active">
                    <div class="form_content">
                        <div class="form_item">
                            <input type="text" placeholder="Ваше ФИО">
                            <input type="text" placeholder="+ 7 ( ____ ) - ___ - __ - __">
                            <input type="email" placeholder="Ваш E-mail">
                            <input type="text" placeholder="Название организации">
                        </div>
                        <div class="form_item">
                            <div class="form_select">
                                <select>
                                    <option>Товары</option>
                                    <option>1</option>
                                    <option>2</option>
                                </select>
                            </div>
                            <div class="form_select">
                                <select>
                                    <option>Количество</option>
                                    <option>1</option>
                                    <option>2</option>
                                </select>
                            </div>
                            <div class="form_btn">
                                <div class="form_policy">
                                    <input type="checkbox" id="form_policy">
                                    <label for="form_policy">
                                        Даю согласие на обработку
                                        персональных данных
                                    </label>
                                </div>
                                <button type="submit" class="btn blue">
                                    отправить
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
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
