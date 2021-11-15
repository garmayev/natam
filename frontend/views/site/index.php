<?php

use frontend\models\Client;
use frontend\models\Order;
use frontend\models\Product;
use frontend\models\Service;
use frontend\models\Ticket;
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
 */

// var_dump(Yii::$app->getAlias('@webroot'));

$this->title = Yii::$app->name;
?>
<main>
    <!-- Раздел "оставить заявку" или "Заказать" -->
    <section class="form" id="form">
        <div class="container-fluid">
            <div class="form_inner">
				<?= Html::img(Url::to("/img/journal.png"), ["class" => "journal", "alt" => "journal"]) ?>
                <div class="form_tab">
                    <button class="active">
                        ОСТАВИТЬ <br/>
                        ЗАЯВКУ
                    </button>
                    <button>ЗАКАЗАТЬ</button>
                </div>
				<?php
				$ticket_form = ActiveForm::begin(["action" => Url::to(["ticket/create"]), "options" => ["class" => ["form_block", "form_submit", "active"]]]);
				$client = new Client();
				?>
                <div class="form_content">
                    <div class="form_item">
						<?= Html::activeTextInput($client, "name", ["placeholder" => $client->getAttributeLabel("name")]) ?>
						<?= Html::activeTextInput($client, "phone", ["placeholder" => $client->getAttributeLabel("phone")]) ?>
                        <?= Html::hiddenInput("Ticket[service_id]", 0) ?>
                    </div>
                    <div class="form_item">
                        <div class="form_btn">
                            <div class="form_policy">
<!--                                <input type="checkbox" id="form_policy"/>-->
                                <label for="form_policy">Нажимая кнопку "Отправть" вы даете свое согласие на обработку персональных данных</label>
                            </div>
                            <button type="submit" class="btn blue">
                                отправить
                            </button>
                        </div>
                    </div>
                </div>
				<?php
				ActiveForm::end();
                $this->registerCssFile("https://cdn.jsdelivr.net/npm/suggestions-jquery@21.6.0/dist/css/suggestions.min.css");
				$this->registerCss("
                        .form_inner {
                            transition: .5s;
                        }
                        .form_inner .form_order .form_content {
                            margin-top: 120px; 
                            max-width: 93%;
                        } 
                        .form_inner .form_order .form_content .form_item {
                            padding: 0 5px;
                        } 
                        .form_inner .form_order .form_content .form_item:last-child {
                            width: 425px
                        }
                        .form_inner .journal {
                            bottom: auto;
                            top: -90px;
                        }
                    ");
				$this->registerJsFile("https://cdn.jsdelivr.net/npm/suggestions-jquery@21.8.0/dist/js/jquery.suggestions.min.js", ["depends" => \yii\web\JqueryAsset::className()]);
				$this->registerJs("
                        $('.about_slider').slick({
                            dots: false,
                            infinite: true,
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            responsive: [
                                {
                                    breakpoint: 1000,
                                    settings: {
                                        slidesToShow: 1,
                                    },
                                },
                                {
                                    breakpoint: 630,
                                    settings: {
                                        slidesToShow: 1,
                                    },
                                },
                            ],
                        });
                        $('#order-address').suggestions({
                            token: '2c9418f4fdb909e7469087c681aac4dd7eca158c',
                            type: 'ADDRESS',
                            constraints: {
                                locations: { region: 'Бурятия' },
                            },
                            onSelect: function(suggestion) {
                                console.log(suggestion);
                            }
                        });
                    ", View::POS_LOAD);
				$order_form = ActiveForm::begin(["action" => Url::to(["order/create"]), "options" => ["class" => ["form_block", "form_order"]]]);
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
						echo Html::dropDownList("Order[product][id][]", null, ArrayHelper::map(Product::find()->all(), "id", "title"), ["prompt" => "Товары"]);
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
						echo Html::endTag("div");
						?>
                        <div class="form_btn">
                            <div class="form_policy">
<!--                                <input type="checkbox" id="form_policy" />-->
                                <label for="form_policy">Нажимая кнопку "Отправть" вы даете свое согласие на обработку персональных данных</label>
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
    </section>
    <!-- Популярные продукты -->
    <section class="product" id="product">
        <div class="container">
            <div class="product_top">
				<?= Html::tag("h2", "ОФОРМИТЬ ЗАКАЗ", ["class" => "title",  "data-aos" => "fade-up"]) ?>
				<?= Html::a("", Url::to("#"), ["class" => "more"]) ?>
            </div>
			<?= ListView::widget([
				"dataProvider" => $productProvider,
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
