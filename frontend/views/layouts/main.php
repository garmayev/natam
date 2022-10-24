<?php

/**
 * @var $this View
 * @var $content string
 */

use frontend\assets\AppAsset;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\web\View;

AppAsset::register($this);
$this->beginPage();

$menu = [
    ["label" => Yii::t("app", "About company"), "url" => Url::to("/site/about")],
    ["label" => "Технические газы", "url" => Url::to("/#product")],
    ["label" => "Наши услуги", "url" => Url::to("/service/index")],
    ["label" => Yii::t("app", "Vacancy"), "url" => Url::to("/vacancy/index")],
    ["label" => "Контакты", "url" => Url::to("/contact")],
    ["label" => "Каталог", "url" => Url::to("/site/addition")]
];

?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>" class="h-100">
    <head>
        <meta name="yandex-verification" content="a152829f9793ec22"/>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="shortcut icon" href="https://natam03.ru/favicon.png"/>
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php
    $this->beginBody();
    ?>
    <div class="home">
        <?= $this->render('/layouts/_header', ["menu" => $menu]); ?>
        <?= $content ?>
        <?= $this->render('/layouts/_footer', ['menu' => $menu]) ?>
    </div>
    <?php
    $this->endBody();
    ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-221543085-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', 'UA-221543085-1');
    </script>
    <script type="text/javascript">(function (c, s, t, r, e, a, m) {
            c[e] = c[e] || function () {
                (c[e].q = c[e].q || []).push(arguments)
            }, c[e].p = r, a = s.createElement(t), m = s.getElementsByTagName(t)[0], a.async = 1, a.src = r, m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://c.sberlead.ru/clickstream.bundle.js', 'csa');
        csa('init', {analyticsId: 'ddd29733-cb08-472a-9035-f89a32cee563'}, true, true);</script>

    </body>
    </html>
<?php $this->endPage();
