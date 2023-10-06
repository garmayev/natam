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
    <span class="copyright">Created by <a href="http://gasgo.pro/">AMG Company</a></span>
</div>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
    (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

    ym(87339199, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true,
        ecommerce:"dataLayer"
    });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/87339199" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->