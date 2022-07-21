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

    <?= Html::a('<span class="logo-mini">APP</span><span class="logo-lg">' . Yii::$app->name . '</span>', '/', ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button" data-widget="pushmenu">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="user-image" alt="User Image"/>
                        <span class="hidden-xs"><?= (Yii::$app->user->identity->profile->name) ? Yii::$app->user->identity->profile->name : Yii::$app->user->identity->username ?></span>
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
