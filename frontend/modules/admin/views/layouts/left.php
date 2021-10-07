<?php

use yii\web\View;
use yii\helpers\Url;

/**
 * @var $this View
 */

$menu = [
	[
		"label" => Yii::t("app", "Admin Panel"),
		"url" => Url::to(["/admin/default/index"]),
		"icon" => "dashboard",
	], [
		"label" => Yii::t("app", "Shop"),
		"icon" => "desktop",
		"items" => [
			[
				"label" => Yii::t("app", "Orders"),
				"url" => Url::to(["/admin/order/index"]),
				"icon" => "bell",
			], [
				"label" => Yii::t("app", "Products"),
				"url" => Url::to(["/admin/product/index"]),
				"icon" => "microchip",
			],
		],
	], [
		"label" => Yii::t("app", "Services"),
		"url" => Url::to(["/admin/service/index"]),
		"icon" => "file-code-o"
	], [
		"label" => Yii::t("app", "News"),
		"url" => Url::to(["/admin/post/index"]),
		"icon" => "circle-o",
	], [
		"label" => Yii::t("app", "Tickets"),
		"url" => Url::to(["/admin/ticket/index"]),
		"icon" => "ticket",
//	], [
//		"label" => "Tracker",
//		"url" => Url::to(["/admin/tracker/index"]),
//		"icon" => "map-marker",
	], [
		"label" => Yii::t("app", "Vacancy"),
		"url" => Url::to(["/admin/vacancy/index"]),
		"icon" => "user",
	], [
		"label" => Yii::t("app", "Users"),
		"url" => Url::to(["/admin/user/index"]),
		"icon" => "user-o"
	], [
		"label" => Yii::t("app", "Settings"),
		"url" => Url::to(["/admin/settings/index"]),
		"icon" => "cog"
	]
]
?>
<aside class="main-sidebar">
    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p>Alexander Pierce</p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
                <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

		<?= dmstr\widgets\Menu::widget(
			['options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
				'items' => $menu,]
		) ?>

    </section>

</aside>
