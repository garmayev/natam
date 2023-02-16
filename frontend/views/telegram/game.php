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
    <script src="https://api-maps.yandex.ru/2.1/?apikey=0bb42c7c-0a9c-4df9-956a-20d4e56e2b6b&lang=ru_RU"
            type="text/javascript"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let tg = window.Telegram.WebApp;
            tg.expand();
            if (tg.initData) {
                $(".log").append($(`<p>${tg.initData.user.id}</p>`));
            } else {
                $(".log").append($(`<p>${tg.initDataUnsafe.user.id}</p>`));
            }
        })
    </script>
    <link rel="stylesheet" href="/css/webapp.css">
</head>
<body>
<div class="cart">
    <h1>Приложение находится на стадии разработки. Приносим свои извинения!</h1>
    <div class="log"></div>
</div>
</body>
</html>