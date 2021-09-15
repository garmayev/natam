<?php
/**
 * @var $menu array
 */

use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\Menu;

?>
<header class="header">
	<div class="container">
		<div class="header_inner">
			<div class="header_left">
				<div class="header_navigation">
					<div class="nav_toggle">
						<span class="nav_toggle-item"></span>
					</div>
					<div class="logo">
						<a href="/">
							<img src="/img/logo.svg" alt="logo" />
						</a>
						<p>
							Продажа и поставка
							<br />
							технических газов
						</p>
					</div>
					<nav class="nav">
						<span class="close"></span>
                        <?= Menu::widget([
                                'items' => $menu
                        ]) ?>
					</nav>
				</div>
				<div class="main_inner">
					<h1 class="main_title">
						ПРОДАЖА И ПОСТАВКА ТЕХНИЧЕСКИХ ГАЗОВ
					</h1>
					<p class="main_text">
						с доставкой и самовывозом по всей России
					</p>
                    <?= Html::a("ОФОРМИТЬ ЗАКАЗ", Url::to("#"), ["class" => ["btn", "blue"], "style" => "padding: 15px 25px;"]) ?>
				</div>
                <div class="main_content">
                    <div class="main_content_inner">
                        <h1 class="main_title">
                            ПРОДАЖА И ПОСТАВКА ТЕХНИЧЕСКИХ ГАЗОВ
                        </h1>
                        <p class="main_text">
                            с доставкой и самовывозом по всей России
                        </p>
                    </div>
                    <a href="#" class="btn blue" style="padding: 15px 20px">ОФОРМИТЬ ЗАКАЗ</a>
                    <p class="header_price">
                        <img src="/img/price.svg" alt="icon">
                        выгодные цены <br>
                        разный объем
                    </p>
                </div>
			</div>
            <?php
                $this->registerJs("$('.header_inner .blue').on('click', (e) => {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $('#form').offset().top
                });
                $('.form_tab > button:last-child').trigger('click');})");
            ?>
			<div class="header_info">
				<a href="tel:+71234567890" class="phone">
					+7 123 456 78 90
				</a>
				<a href="#" class="btn recall">
					<img src="/img/phone.svg" alt="phone" />
					Заказать звонок
				</a>
				<a href="/admin/" class="profile">
					<img src="/img/profile.svg" alt="profile" />
					<span>ЛИЧНЫЙ КАБИНЕТ</span>
				</a>
				<p class="header_price">
					<img src="/img/price.svg" alt="icon" />
					выгодные цены <br />
					разный объем
				</p>
			</div>
		</div>
	</div>
</header>
