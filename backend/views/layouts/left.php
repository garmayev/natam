<?php

use common\models\Client;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\Url;

/**
 * @var $this View
 */

//if (!Yii::$app->user->can("employee")) {
//	$clientInfo = Client::findOne(['phone' => Yii::$app->user->identity->username]);
//	if (!$clientInfo) {
//		$menu = [
//			[
//				"label" => Yii::t("app", "Admin Panel"),
//				"url" => Url::to(["/default/index"]),
//				"icon" => "bell",
//			]
//		];
//	} else {
//		$menu = [
//			[
//				"label" => Yii::t("app", "Admin Panel"),
//				"url" => Url::to(["/default/index"]),
//				"icon" => "bell",
//			], [
//				"label" => Yii::t("app", "Orders"),
//				"url" => Url::to(["/order/index"]),
//				"icon" => "bell",
//			], [
//				"label" => Yii::t("app", "Tickets"),
//				"url" => Url::to(["/ticket/index"]),
//				"icon" => "ticket",
//			], [
//				"label" => Yii::t("app", "Client info"),
//				"url" => Url::to(["client/view", "id" => $clientInfo->id]),
//				"visible" => false,
//			]
//		];
//	}
//} else {
//
//	$menu = [
//		[
//			"label" => Yii::t("app", "Admin Panel"),
//			"url" => Url::to(["/default/index"]),
//			"icon" => "dashboard",
//		],
//        [
//			"label" => Yii::t("app", "Shop"),
//			"icon" => "desktop",
//			"items" => [
//				[
//					"label" => Yii::t("app", "Products"),
//					"url" => Url::to(["/category/index"]),
//					"icon" => "microchip",
//				], [
//					"label" => Yii::t("app", "Clients"),
//					"url" => Url::to(["/client/index"]),
//					"icon" => "user-circle-o",
//				]
//			],
//		],
//        [
//			"label" => Yii::t("app", "Orders"),
//			"url" => Url::to(["/order/index"]),
//			"icon" => "bell",
//		],
//        [
//			"label" => Yii::t("app", "Site control"),
//			"items" => [
//				[
//					"label" => Yii::t("app", "Services"),
//					"url" => Url::to(["/service/index"]),
//					"icon" => "file-code-o"
//				], [
//					"label" => Yii::t("app", "News"),
//					"url" => Url::to(["/post/index"]),
//					"icon" => "circle-o",
//				], [
//					"label" => Yii::t("app", "Tickets"),
//					"url" => Url::to(["/ticket/index"]),
//					"icon" => "ticket",
//				], [
//					"label" => Yii::t("app", "Vacancy"),
//					"url" => Url::to(["/vacancy/index"]),
//					"icon" => "user",
//				],
//			]
//		],
//        [
//			"label" => Yii::t("app", "Analytic"),
//			"icon" => "bar-chart-o",
//			"items" => [
//				[
//					"label" => Yii::t("app", "Analytics by orders"),
//					"url" => Url::to(["/analytics/orders"])
//				],
//				[
//					"label" => Yii::t("app", "Analytics by employee"),
//					"url" => Url::to(["/analytics/employee"])
//				]
//			]
//		],
//        [
//			"label" => Yii::t("app", "Settings"),
//			"icon" => "cog",
//			"items" => [
//				[
//					"label" => Yii::t("app", "Notify Settings"),
//					"url" => Url::to(["/settings/index"]),
//				], [
//					"label" => Yii::t("user", "Users"),
//					"items" => [
//						[
//							"label" => Yii::t("yii2mod.rbac", "Assignments"),
//							"url" => Url::to(["/rbac/assignment/index"])
//						], [
//							"label" => Yii::t("yii2mod.rbac", "Routes"),
//							"url" => Url::to(["/rbac/route/index"])
//						], [
//							"label" => Yii::t("yii2mod.rbac", "Permissions"),
//							"url" => Url::to(["/rbac/permission/index"])
//						], [
//							"label" => Yii::t("yii2mod.rbac", "Roles"),
//							"url" => Url::to(["/rbac/role/index"])
//						],
//					]
//				], [
//					"label" => Yii::t("app", "Staff"),
//					"url" => Url::to(["/staff/index"])
//				]
//			]
//		]
//	];
//	if ($clientInfo = Yii::$app->user->identity->client) {
//		$menu[] = [
//			"label" => Yii::t("app", "Client info"),
//			"url" => Url::to(["client/view", "id" => Yii::$app->user->identity->client->id])
//		];
//	}
//}
$menu = [
    [
        "label" => Yii::t("app", "Admin Panel"),
        "url" => Url::to(["/default/index"]),
        "icon" => "dashboard",
        "active" => Yii::$app->controller->id === "default",
    ],
    [
        "label" => Yii::t("app", "Shop"),
        "icon" => "desktop",
        "items" => [
            [
                "label" => Yii::t("app", "Products"),
                "url" => Url::to(["/category/index"]),
                "icon" => "microchip",
                "active" => Yii::$app->controller->id === "category",
            ], [
                "label" => Yii::t("app", "Clients"),
                "url" => Url::to(["/client/index"]),
                "icon" => "user-circle-o",
                "active" => Yii::$app->controller->id === "client",
            ]
        ],
        "visible" => Yii::$app->user->can('employee'),
    ],
    [
        "label" => Yii::t("app", "Orders"),
        "url" => Url::to(["/order/index"]),
        "icon" => "bell",
        "active" => Yii::$app->controller->id === "order",
    ],
    [
        "label" => Yii::t('app', 'Companies'),
        'url' => Url::to(["company/index"]),
        "icon" => "address-book",
        'active' => Yii::$app->controller->id === "company",
    ],
    [
        "label" => Yii::t("app", "Site control"),
        "items" => [
            [
                "label" => Yii::t("app", "Services"),
                "url" => Url::to(["/service/index"]),
                "icon" => "file-code-o",
                "active" => Yii::$app->controller->id === "service",
            ], [
                "label" => Yii::t("app", "News"),
                "url" => Url::to(["/post/index"]),
                "icon" => "circle-o",
                "active" => Yii::$app->controller->id === "post",
            ], [
                "label" => Yii::t("app", "Vacancy"),
                "url" => Url::to(["/vacancy/index"]),
                "icon" => "user",
                "active" => Yii::$app->controller->id === "vacancy",
            ],
        ],
        "visible" => Yii::$app->user->can("employee")
    ],
    [
        "label" => Yii::t("app", "Tickets"),
        "url" => Url::to(["/ticket/index"]),
        "icon" => "ticket",
        "active" => Yii::$app->controller->id === "ticket",
    ],
    [
        "label" => Yii::t("app", "Analytic"),
        "icon" => "bar-chart-o",
        "items" => [
            [
                "label" => Yii::t("app", "Analytics by orders"),
                "url" => Url::to(["/analytics/orders"]),
                "active" => Yii::$app->controller->action->id === "orders" && Yii::$app->controller->id === "analytics",
            ],
            [
                "label" => Yii::t("app", "Analytics by employee"),
                "url" => Url::to(["/analytics/employee"]),
                "active" => Yii::$app->controller->action->id === "employee" && Yii::$app->controller->id === "analytics",
            ], [
                "label" => Yii::t("app", "Fuel consumption"),
                "url" => Url::to(["analytics/fuel"]),
                "active" => Yii::$app->controller->action->id === "fuel" && Yii::$app->controller->id === "analytics",
            ]
        ],
        "active" => Yii::$app->controller->id === "analytics",
        "visible" => Yii::$app->user->can("admin")
    ],
    [
        "label" => Yii::t("app", "Settings"),
        "icon" => "cog",
        "items" => [
            [
                "label" => Yii::t("app", "Notify Settings"),
                "url" => Url::to(["/settings/index"]),
                "active" => Yii::$app->controller->id == "settings",
            ], [
                "label" => Yii::t("user", "Users"),
                "items" => [
                    [
                        "label" => Yii::t("yii2mod.rbac", "Assignments"),
                        "url" => Url::to(["/rbac/assignment/index"]),
                        "active" => Yii::$app->controller->id == "assignment"
                    ], [
                        "label" => Yii::t("yii2mod.rbac", "Routes"),
                        "url" => Url::to(["/rbac/route/index"]),
                        "active" => Yii::$app->controller->id == "route"
                    ], [
                        "label" => Yii::t("yii2mod.rbac", "Permissions"),
                        "url" => Url::to(["/rbac/permission/index"]),
                        "active" => Yii::$app->controller->id == "permission"
                    ], [
                        "label" => Yii::t("yii2mod.rbac", "Roles"),
                        "url" => Url::to(["/rbac/role/index"]),
                        "active" => Yii::$app->controller->id == "role"
                    ], [
                        "label" => Yii::t('yii2mod.rbac', 'Rules'),
                        "url" => Url::to(["/rbac/rule/index"]),
                        "active" => Yii::$app->controller->id == "rule",
                    ]
                ]
            ], [
                "label" => Yii::t("app", "Staff"),
                "url" => Url::to(["/staff/index"]),
                "active" => Yii::$app->controller->id == "staff",
            ]
        ],
        "visible" => Yii::$app->user->can("admin")
    ]
];
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
                $employee = \common\models\staff\Employee::findOne(["user_id" => Yii::$app->user->id]);
                if ($employee !== null) {
                    echo Html::tag("p", "{$employee->name} {$employee->family}");
                } else {
                    echo Html::tag("p", (Yii::$app->user->identity->profile->name) ? Yii::$app->user->identity->profile->name : Yii::$app->user->identity->username);
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
