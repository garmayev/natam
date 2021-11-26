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

$menu = [
    ["label" => Yii::t("app", "About company"), "url" => Url::to("/about")],
	["label" => Yii::t("app", "Vacancy"), "url" => Url::to("/vacancy/index")],
	["label" => "Технические газы", "url" => Url::to("/product/index")],
	["label" => "Наши услуги", "url" => Url::to("/service/index")],
	["label" => "Контакты", "url" => Url::to("/contact")],
];

AppAsset::register($this);
$this->beginPage();


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
$count = count(Yii::$app->cart->getItems());
Pjax::begin(["id" => "cart-pjax", "timeout" => 1000]);
echo Html::beginTag("div", ["class" => ["cart", ($count > 0) ? "visible" : ""]]);
echo Html::beginTag("a", ["href" => Url::to(["cart/index"]), "rel" => "modal:open"]);
echo Html::img("/img/cart.png").Html::tag("span", $count, ["class" => "badges"]);
echo Html::endTag("a");
echo Html::endTag("div");
Pjax::end();
if ( !empty($success = Yii::$app->session->getFlash("success")) ) {
    echo Html::tag("div", $success, ["class" => 'alert']);
}
echo $this->render('_header', ["menu" => $menu]);
echo $content;
echo $this->render("_footer", ["menu" => $menu]);
$this->endBody();
?>
</body>
</html>
<?php $this->endPage();
