<?php

/* @var $this \yii\web\View */

/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;

//AppAsset::register($this);
\backend\assets\LoginAsset::register($this);
$this->registerCss(<<< CSS
    .login-form-content::after {
        background: none;
    }
CSS )
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="login-page__body">
<?php $this->beginBody() ?>
<main class="login-main">
    <div class="login-size-wrapper">
        <div class="login-form-wrapper">
            <a href="/" class="login-page__logo-prev">
                <img class="login-page__logo-img" src="/img/logo.svg" alt="logo">
            </a>
            <div class="login-form-slide">
                <div class="login-form-content">
                    <h1 class="login__title">Вход</h1>
                    <p class="login__text">Добро пожаловать в <img src="/img/logo_amgsystems_new.svg" height="23px"></p>
                    <?php
                    $form = \yii\widgets\ActiveForm::begin([
                        "options" => ["class" => "login-form"],
                    ]);
                    ?>
                    <div class="authorization-fields">
                        <div class="login-group">
                            <input id="log" type="text" name="login-form[login]" class="input-login" placeholder=" ">
                            <label for="log" class="label-login">Логин</label>
                        </div>
                        <div class="password-group">
                            <input id="password" type="password" name="login-form[password]" class="input-password"
                                   placeholder=" ">
                            <label for="password" class="label-password">Пароль</label>
                        </div>
                    </div>
                    <div class="button-group">
                        <button type="submit" class="login-button">Войти</button>
                    </div>
                    <?php
                    \yii\widgets\ActiveForm::end();
                    ?>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="login-footer">
    <div class="login-size-wrapper">
        <div class="login-footer-info">
            <div class="login-footer-company">
                <a href="#" class="login-footer__agreement">Пользовательское соглашение</a>
            </div>
            <div class="login-footer__contacts">
                <a href="tel:88001012316" class="login-footer__contacts-text">8 800 550 61 10</a>
            </div>
        </div>
    </div>
</footer>
<?php $this->endBody() ?>
</body>
<?php $this->endPage() ?>

</html>
