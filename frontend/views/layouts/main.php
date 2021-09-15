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

$menu = [
    ["label" => "О компании", "url" => Url::to("/about")],
	["label" => "Вакансии", "url" => Url::to("/vacancy/index")],
	["label" => "Технические газы", "url" => Url::to("/product/index")],
	["label" => "Наши услуги", "url" => Url::to("/service/index")],
	["label" => "Контакты", "url" => Url::to("/contact")],
];

AppAsset::register($this);
$this->beginPage();

/**
 * @todo Модели "Заявки" и "Заказы"
 * @todo Доделать раздел вакансии
 * @todo Админка
 */
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="home">
<?php
$this->beginBody();
echo $this->render('_header', ["menu" => $menu]);
echo $content;
echo $this->render("_footer", ["menu" => $menu]);
$this->endBody();
?>
</body>
</html>
<?php $this->endPage();
