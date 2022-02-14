<?php

use yii\helpers\Html;
use yii\web\View;
use yii\helpers\Url;

/**
 * @var $this View
 */

if (Yii::$app->user->can("person")) {
	$menu = [
		[
			"label" => Yii::t("app", "Admin Panel"),
			"url" => Url::to(["/default/index"]),
			"icon" => "dashboard",
		], [
			"label" => Yii::t("app", "Shop"),
			"icon" => "desktop",
			"active" => true,
			"items" => [
				[
					"label" => Yii::t("app", "Orders"),
					"url" => Url::to(["/order/index"]),
					"icon" => "bell",
				]
			],
		], [
			"label" => Yii::t("app", "Tickets"),
			"url" => Url::to(["/ticket/index"]),
			"icon" => "ticket",
		], [
			"label" => Yii::t("app", "Vacancy"),
			"url" => Url::to(["/vacancy/index"]),
			"icon" => "user",
		]
	];
}

//$menu = [
//	[
//		"label" => Yii::t("app", "Admin Panel"),
//		"url" => Url::to(["/default/index"]),
//		"icon" => "dashboard",
//	], [
//		"label" => Yii::t("app", "Shop"),
//		"icon" => "desktop",
//		"items" => [
//			[
//				"label" => Yii::t("app", "Orders"),
//				"url" => Url::to(["/order/index"]),
//				"icon" => "bell",
//			], [
//				"label" => Yii::t("app", "Products"),
//				"url" => Url::to(["/category/index"]),
//				"icon" => "microchip",
//			], [
//				"label" => Yii::t("app", "Clients"),
//				"url" => Url::to(["/client/index"]),
//				"icon" => "user-circle-o",
//			]
//		],
//	], [
//		"label" => Yii::t("app", "Services"),
//		"url" => Url::to(["/service/index"]),
//		"icon" => "file-code-o"
//	], [
//		"label" => Yii::t("app", "News"),
//		"url" => Url::to(["/post/index"]),
//		"icon" => "circle-o",
//	], [
//		"label" => Yii::t("app", "Tickets"),
//		"url" => Url::to(["/ticket/index"]),
//		"icon" => "ticket",
//	], [
//		"label" => Yii::t("app", "Vacancy"),
//		"url" => Url::to(["/vacancy/index"]),
//		"icon" => "user",
//	], [
//		"label" => Yii::t("app", "Users"),
//		"url" => Url::to(["/rbac/assignment"]),
//		"icon" => "user-o"
//	], [
//		"label" => Yii::t("app", "Settings"),
//		"url" => Url::to(["/settings/index"]),
//		"icon" => "cog"
//	]
//]
?>
<aside class="main-sidebar">
    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
				<?php
				$employee = \garmayev\staff\models\Employee::findOne(["user_id" => Yii::$app->user->id]);
				if ($employee !== null) {
					echo Html::tag("p", "{$employee->name} {$employee->family}");
				} else {
					echo Html::tag("p", Yii::$app->user->identity->username);
				}
				?>

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
