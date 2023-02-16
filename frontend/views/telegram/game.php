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
    <script src="/js/telegram-game.js" type="module">
        import {User} from "/js/telegram-game";

        let tg = window.Telegram.WebApp,
            user = new User(document.querySelector("body > .container-fluid"), tg.initDataUnsafe.id);
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/webapp.css">
</head>
<body>
<div class="container-fluid">
    <div class="row logger" style="color: white">

    </div>
    <div class="row p-3 login" style="display: none;">
        <form action="/user/login" class="container-fluid login-form" autocomplete="off">
            <input type="hidden" id="csrf">
            <input type="hidden" id="chat_id">
            <div class="form-group col-12">
                <input class="form-control col-12" type="text" name="login-form[login]"
                       placeholder="<?= Yii::t('user', 'Login') ?>" autocomplete="new-login"/>
            </div>
            <div class="form-group col-12">
                <input class="form-control col-12" type="password" name="login-form[password]"
                       placeholder="<?= Yii::t('user', 'Password') ?>" autocomplete="new-password"/>
            </div>
            <div class="form-group text-center col-12">
                <button class="btn btn-success"><?= Yii::t('user', 'Sign in') ?></button>
            </div>
        </form>
    </div>
</div>
<div class="log"></div>
</body>
</html>