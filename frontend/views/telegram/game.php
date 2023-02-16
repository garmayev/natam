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
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
    <script type="module" src="/js/telegram-game.js"></script>
    <script type="module">
        import {Order, User} from "/js/telegram-game.js";

        let tg = window.Telegram.WebApp;
        let user = new User(document.querySelector("body > .container-fluid"), tg.initDataUnsafe.user ? tg.initDataUnsafe.user.id : "443353023");

        user.on(User.EVENT_LOGGED, function (e) {
            console.log(e);
            let orders = Order.get(this);
            console.log(orders);
        }.bind(user))

        user.init();
        // console.log(user);
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/webapp.css">
</head>
<body>
<div class="container-fluid">
</div>
<div class="log"></div>
</body>
</html>