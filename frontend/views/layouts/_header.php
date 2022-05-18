<?php
/**
 * @var $menu array
 */

use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\Menu;

?>
<div class="preloader">
    <div class="preloader-dots">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>
</div>
<div class="shadow"></div>
<header class="header">
	<div class="container">
		<div class="header_inner">
			<div class="header_left">
				<div class="header_navigation" data-aos="fade-down">
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
				<div class="main_inner" data-aos="fade-up">
					<h1 class="main_title">
						ПРОДАЖА И ПОСТАВКА ТЕХНИЧЕСКИХ ГАЗОВ
					</h1>
					<p class="main_text">
						с доставкой и самовывозом по всей России
					</p>
                    <?= Html::a("ОФОРМИТЬ ЗАКАЗ", Url::to("/#product"), ["class" => ["btn", "blue"], "style" => "padding: 15px 25px;"]) ?>
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
                    <a href="/#product" class="btn blue" style="padding: 15px 20px">ОФОРМИТЬ ЗАКАЗ</a>
                    <p class="header_price">
                        <img src="/img/price.svg" alt="icon">
                        выгодные цены <br>
                        разный объем
                    </p>
                </div>
			</div>
			<div class="header_info" data-aos="fade-left">
				<a href="tel:+73012204056" class="phone">
					+7 3012 20 40 56
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
