<?php

/**
 * @var $this \yii\web\View
 * @var $content string
 */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Url;
use yii\widgets\Pjax;

AppAsset::register($this);
$this->beginPage();

$menu = [
    ["label" => Yii::t("app", "About company"), "url" => Url::to("/site/about")],
    ["label" => Yii::t("app", "Vacancy"), "url" => Url::to("/vacancy/index")],
    ["label" => "Технические газы", "url" => Url::to("/#product")],
    ["label" => "Наши услуги", "url" => Url::to("/service/index")],
    ["label" => "Контакты", "url" => Url::to("/contact")],
    ["label" => "Каталог", "url" => Url::to("/site/addition")]
];

?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta name="yandex-verification" content="a152829f9793ec22" />
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="https://natam03.ru/favicon.png" />
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
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-221543085-1');
</script>
</body>
</html>
<?php $this->endPage();
