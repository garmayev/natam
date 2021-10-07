<?php

use yii\base\View;
use yii\widgets\Menu;

/**
 * @var $this View
 * @var $menu array
 */
?>
<footer class="footer">
    <div class="container">
        <div class="footer_inner">
            <div class="footer_content">
                <nav class="footer_nav">
					<?= Menu::widget([
						"items" => $menu
					]) ?>
                </nav>
                <?php
                    if ( Yii::$app->request->getUrl() === "/contact" ) {
                ?>
                        <div class="footer_info">
                            <div class="footer_info_left">
                                <p>
                                    <img src="/img/geo.svg" alt="geo"/>
                                    Юр. адрес: 364051,Чеченская Республика,
                                    г.Грозный, ул.Брагунская,дом 9,офис 221
                                </p>
                                <p>
                                    <img src="/img/geo.svg" alt="geo"/>
                                    Факт.адрес: 670045 г.Улан-Удэ, п.Полигон,
                                    502км База «Разнооптторг-К»
                                </p>
                                <p>
                                    <img src="/img/phone-2.svg" alt="phone "/>
                                    Тел.факс:
                                    <a href="tel:+83012467456"
                                    >8 (3012) 46-74-56,20-40-56</a
                                    >
                                </p>
                                <a href="#" class="policy"
                                >Политика конфиденциальности</a
                                >
                            </div>
                            <div class="footer_info_right">
                                <p>Реквизиты компании</p>
                                <p>
                                    ИНН/КПП 2014014335/2014011001
                                    <br/>
                                    ОКПО 05769863
                                    <br/>
                                    ОКАТО 96401364000
                                    <br/>
                                    ОКВЭД 46.71
                                    <br/>
                                    ОКФС 16
                                    <br/>
                                    ОКОПФ 12300
                                </p>
                            </div>
                        </div>
                <?php
                    }
                ?>
            </div>
            <div class="footer_logo">
                <div class="logo">
                    <a href="#">
                        <img src="/img/logo.svg" alt="logo"/>
                    </a>
                    <p>
                        Продажа и поставка
                        <br/>
                        технических газов
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
<div style="text-align: center; padding: 5px 0; background: #054670; color: #fff;">
    <span class="copyright">Created by <a href="https://amgexpert.com/">AMGCompany</a></span>
</div>

<div id="modal">
    <div class="modal_shadow">&nbsp;</div>
    <div class="container-fluid">
        <div class="form_inner">
            <img class="journal" src="/img/journal.png" alt="journal">
            <form class="form_block form_submit active">
                <label>
                    Наш менеджер свяжется с вами в ближайшее время
                </label>
                <div class="form_content">
                    <div class="form_item">
                        <input type="text" placeholder="Ваше ФИО">
                        <input type="text" placeholder="+ 7 ( ____ ) - ___ - __ - __">
                    </div>
                    <div class="form_item">
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
</div>