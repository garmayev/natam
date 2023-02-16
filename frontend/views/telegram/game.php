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

    <script>
	let token = "";
        document.addEventListener("DOMContentLoaded", () => {
            let tg = window.Telegram.WebApp;
            $.ajax({
                url: "/api/default/option",
		method: "GET",
		data: {
			chat_id: tg.initDataUnsafe.user.id
		},
            }).then(response => {
                tg.expand();
		if ( response.ok ) {
		    console.log(response);
		    token = response.access_token;
		    $.ajax({
			url: "/api/default/login", 
			data: {access_token: token},
		    }).then(response => console.log(response));
		} else {
		    document.querySelector(".login").setAttribute("style", "display: block;");
		    console.log("Unknown user");
		}
            }).catch(error => {
                // tg.close();
            })
        })
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
        <form action="/api/default/login" class="container-fluid">
            <div class="form-group col-12">
                <input class="form-control col-12" type="text" name="User[login]"
                       placeholder="<?= Yii::t('user', 'Login') ?>"/>
            </div>
            <div class="form-group col-12">
                <input class="form-control col-12" type="password" name="User[password]"
                       placeholder="<?= Yii::t('user', 'Password') ?>"/>
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