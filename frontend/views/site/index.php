<?php

use common\models\Client;
use common\models\Order;
use common\models\Product;
use common\models\Service;
use common\models\Ticket;
use kartik\datetime\DateTimePicker;
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
				<?= Html::beginForm(["/ticket/create"], "post", ["class" => ["form_block", "form_submit"]]); ?>
                <!-- <form class="form_block form_submit"> -->
                <input type="hidden" name="Ticket[service_id]" value="0">
				<?php
				$ticket = new Ticket();
				$client = new Client();
				echo Html::beginTag("div", ["class" => "form_content"]);
				echo Html::beginTag("div", ["class" => "form_item"]);
				echo Html::textInput("Client[name]", "", ["placeholder" => "Ваше ФИО", "id" => "client-name", "required" => true]);
				echo Html::textInput("Client[phone]", "", ["placeholder" => "Ваш номер телефона", "id" => "client-phone", "required" => true]);
				echo Html::textInput("Client[email]", "", ["placeholder" => "Ваш E-mail", "type" => "email", "id" => "client-email"]);
				echo Html::textInput("Client[company]", "", ["placeholder" => "Название организации", "id" => "client-company"]);
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
                <!-- </form> -->
				<?= Html::endForm() ?>
				<?= Html::beginForm(["/order/create"], "post", ["class" => ["form_block", "form_order", "active"]]) ?>
                <!--                <form class="form_block form_order active">-->
                <div class="form_content step" data-index="1">
                    <div class="form_item"></div>
                    <div class="form_item"></div>
                </div>
                <div class="form_content step" data-index="2">
                    <div class="form_item">
                        <input type="text" id="client-name" name="Client[name]" placeholder="Ваше ФИО">
                        <input type="text" id="client-phone" name="Client[phone]" placeholder="Ваш номер телефона">
                        <input type="email" name="Client[email]" placeholder="Ваш E-mail">
                    </div>
                    <div class="form_item">
                        <textarea name="Order[comment]" placeholder="Комментарий" rows="5" style="border-radius: 10px; padding: 18px;"></textarea>
                        <div class="form_btn">
                            <a href="#" class="btn blue prev" style="float: left">
                                Предыдущий шаг
                            </a>
                            <a href="#" class="btn blue next" style="float: right">
                                Следующий шаг
                            </a>
                        </div>
                    </div>
                </div>
                <div class="form_content step" data-index="3">
                    <div class="date-address" style="display: flex;">
                        <div class="form_item">
							<?=
							DateTimePicker::widget([
								'name' => 'Order[delivery_date]',
								'type' => DateTimePicker::TYPE_INPUT,
								'options' => [
									'class' => 'form',
									'autocomplete' => 'qwsedrfrgtghyhuj',
									'id' => 'order-delivery_date',
									'placeholder' => Yii::t('app', 'Delivery Date')
								],
								'pluginOptions' => [
									'startDate' => date('Y-m-d'),
									'daysOfWeekDisabled' => [0],
									'minuteStep' => 30,
									'autoclose' => true,
                                    'hoursDisabled' => '0,1,2,3,4,5,6,7,8,18,19,20,21,22,23,24',
								],
								'pluginEvents' => [
                                    'changeMode' => "function (e) {
                                        let picker = $(this).datetimepicker($(this).attr('data-krajee-datetimepicker'));
                                        if ( (e.newViewMode === 1) && (e.date.getDay() === 6) ) {
//                                            picker.data('datetimepicker').setHoursDisabled('0,1,2,3,4,5,6,7,8,14,15,16,17,18,19,20,21,22,23,24');
                                        } else {
//                                            picker.data('datetimepicker').setHoursDisabled('0,1,2,3,4,5,6,7,8,18,19,20,21,22,23,24');
                                        }
                                    }",
								]
							]);

							?>
                        </div>
                        <div class="form_item">
                            <input type="text" id="order-address" class="form"
                                   name="Order[address]" value="" placeholder="Адрес доставки">
                            <input type="hidden" name="Location[title]" id="location-title" "="">
                            <input type="hidden" name="Location[latitude]" id="location-latitude">
                            <input type="hidden" name="Location[longitude]" id="location-logintude">
                        </div>
                    </div>
                    <div id="map" style="height: 250px; min-width: 100%; margin-bottom: 20px;"></div>
                    <div class="form_btn">
                        <a href="#" class="btn blue prev" style="float: left;">
                            Предыдущий шаг
                        </a>
                        <button type="submit" class="btn blue next" style="float: right;">
                            Отправить
                        </button>
                    </div>
                </div>
                <!--                </form>-->
				<?= Html::endForm() ?>
            </div>
        </div>
    </section>
    <!-- Популярные продукты -->
    <section class="product" id="product">
        <div class="container">
            <div class="product_top">
				<?= Html::tag("h2", "ОФОРМИТЬ ЗАКАЗ", ["class" => "title", "data-aos" => "fade-up"]) ?>
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
				<?= Html::tag("h2", "НОВОСТИ", ["class" => "title", "data-aos" => "fade-up"]) ?>
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
