<?php

use common\models\Order;
use common\models\Ticket;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var $content string
 */

$tickets = Ticket::find()->where(["status" => Ticket::STATUS_OPEN])->all();
$orders = Order::find()->all();
?>
<header class="main-header">

    <?= Html::a('<span class="logo-mini">APP</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button" data-widget="pushmenu">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">

                <!-- Messages: style can be found in dropdown.less-->
                <li class="dropdown messages-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-envelope-o"></i>
                        <span class="label label-success"><?= count($tickets) ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header"><?= Yii::t("app", "You have {n} messages", ["n" => count(Ticket::find()->all())]) ?></li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                <?php
                                    foreach ($tickets as $ticket) {
//                                        Yii::error($ticket->client->name);
					if (isset($ticket->client)) {
                                        $message = "<div class='pull-left'><img src='{$directoryAsset}/img/user3-128x128.jpg'></div>
<h4>{$ticket->client->name}</h4>
<p>{$ticket->client->phone}</p>";
                                        echo Html::tag("li", Html::a($message, ["/admin/ticket/view", "id" => $ticket->id]));
					}
                                    }
                                ?>
                            </ul>
                        </li>
                        <li class="footer"><?= Html::a(Yii::t("app", "See all messages"), ["/admin/ticket/index"]) ?></li>
                    </ul>
                </li>
                <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-warning"><?= count($orders) ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header"><?= Yii::t("app", "You have {n} new orders", ["n" => count($orders)]) ?></li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                <?php
                                    foreach ($orders as $order)
                                    {
					if ( isset($order->client) ) {
                                        $message = "<i class=\"fa fa-shopping-cart text-aqua\"></i> Заказ #{$order->id}<p>{$order->client->name}</p><small>Общая стоимость: {$order->price}</small>";
					}
                                        echo Html::tag("li", Html::a($message, ["/admin/order/view", "id" => $order->id]));
                                    }
                                ?>
                            </ul>
                        </li>
                        <li class="footer"><a href="/admin/order/index">View all</a></li>
                    </ul>
                </li>

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="user-image" alt="User Image"/>
                        <span class="hidden-xs"><?= Yii::$app->user->identity->username ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle"
                                 alt="User Image"/>
                            <?php
                                $employee = \garmayev\staff\models\Employee::findOne(["user_id" => Yii::$app->user->id]);
                                if ( $employee !== null ) {
	                                echo Html::tag("p", "{$employee->name} {$employee->family}" . Html::tag("small", Yii::$app->formatter->asDate(Yii::$app->user->identity->created_at)));
                                } else {
                                    echo Html::tag("p", Yii::$app->user->identity->username . Html::tag("small", Yii::$app->formatter->asDate(Yii::$app->user->identity->created_at)));
                                }
                            ?>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <?= Html::a(Yii::t("app", "Profile"), ["/user/settings/profile"], ["class" => ["btn", "btn-default", "btn-flat"]]) ?>
                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    Yii::t("user", 'Logout'),
                                    ['/user/security/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
