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
            let orders = Order.get(this);
            Order.buildTable(document.querySelector("body > .main"), orders);
            console.log(orders);
        }.bind(user))

        user.init();
        // console.log(user);
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/webapp.css">
</head>
<body style="overflow: auto" class="bg-white">
<div class="main">
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?= Yii::t("app", "Create Order") ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t("app", "Close") ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="datetime-local" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal"><?= Yii::t("app", "Cancel") ?></button>
                <button type="button" class="btn btn-primary"><?= Yii::t("app", "Save") ?></button>
            </div>
        </div>
    </div>
</div>
</body>
</html>