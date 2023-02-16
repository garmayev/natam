<?php

use yii\data\ActiveDataProvider;
use yii\web\View;


/**
 * @var $this View
 * @var $models ActiveDataProvider
 */

?>
<html>
<head>
    <script src="//telegram.org/js/telegram-web-app.js"></script>
    <script src="//kit.fontawesome.com/aa23fe1476.js"></script>
    <script src="//api-maps.yandex.ru/2.1/?apikey=0bb42c7c-0a9c-4df9-956a-20d4e56e2b6b&lang=ru_RU"
            type="text/javascript"></script>
    <script src="//code.jquery.com/jquery-3.6.3.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            $.ajax({
                url: "/api/default/option"
            }).then(response => {
                console.log(response);
                let tg = window.Telegram.WebApp;
                tg.expand();
                if (tg.initData) {
                    document.querySelector(".log").textContent += `<p>${tg.initData.user.id}</p>`;
                } else {
                    document.querySelector(".log").textContent += `<p>${tg.initDataUnsafe.user.id}</p>`;
                }
            })
        })
    </script>
    <link rel="stylesheet" href="/css/webapp.css">
</head>
<body>
<div class="slide">
    <input type="text" name="User[login]" placeholder="<?= Yii::t('user', 'Login') ?>"/>
    <input type="password" name="User[password]" placeholder="<?= Yii::t('user', 'Password') ?>"/>
    <div class="log"></div>
</div>
<div class="slide">

</div>
</body>
</html>