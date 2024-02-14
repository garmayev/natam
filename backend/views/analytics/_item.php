<?php

use common\models\Order;
use common\models\staff\Employee;
use common\models\TelegramMessage;
use yii\helpers\Html;


/**
 * @var $model Order
 */

if ($model->messages) {
    $address = isset($model->location) ? $model->location->title : $model->address;
    echo Html::a(Html::tag('h4', Yii::t('app', 'Order #{id}', ['id' => $model->id]) . ' (' . Html::tag('i', $address) . ') ' . Html::tag('em', "({$model->getStatusName()})", ['class' => 'small'])), ['order/view', 'id' => $model->id]);
    echo Html::beginTag('div', ['class' => 'row']);
    $class = 'panel_default';
    switch ($model->status) {
        case Order::STATUS_COMPLETE:
            $class = 'panel-success';
            break;
        case Order::STATUS_CANCEL:
            $class = 'panel-danger';
            break;
        case Order::STATUS_HOLD:
            $class = 'panel-warning';
            break;
    }
    foreach (Order::getStatusList() as $key => $status) {
        $telegram_message = TelegramMessage::find()
            ->where(['order_id' => $model->id])
            ->andWhere(['order_status' => $key])
            ->one();
        if (!empty($telegram_message)) {
            ?>
            <div class="panel <?= $class ?>" style="width: 31%; margin: 0 .5%; display: inline-block"
                 data-status="<?= $model->status ?>">
                <div class="panel-heading"><?= $status ?></div>
                <div class="panel-body">
                    <?php
                    if ($telegram_message->status === TelegramMessage::STATUS_CLOSED && $telegram_message->updatedBy) {
                        if (isset($telegram_message->updatedBy->employee)) {
                            if ($telegram_message->updated_by === $telegram_message->created_by) {
                                echo "<p>Открыл этап: {$telegram_message->updatedBy->employee->getFullname()}</p>";

                            } else {
                                echo "<p>Закрыл этап: {$telegram_message->updatedBy->employee->getFullname()}</p>";
                            }
                        } else {
                            echo "<p>{$telegram_message->updatedBy->username}</p>";
                        }
                    } else if (isset($telegram_message->created_by)) {
                        if ($telegram_message->order_status === Order::STATUS_DELIVERY) {
                            $e = Employee::findOne(['chat_id' => $telegram_message->chat_id]);
                            if (isset($e))
                                echo "<p>Заявка отправлена: {$e->getFullname()}</p>";
                        } else {
                            $employee = $telegram_message->createdBy->employee;
                            if (isset($employee)) 
                                echo "<p>Открыл этап: {$employee->getFullname()}</p>";
                        }
                    } else {
                        $e = Employee::findOne(['chat_id' => $telegram_message->chat_id]);
                        echo "<p>Заявка отправлена: {$e->getFullname()}</p>";
//				echo "<p>Заказали на сайте</p>";
                    }
                    ?>
                    <?= Html::tag('p', Yii::t('app', 'Created At') . ": " . Yii::$app->formatter->asDatetime($telegram_message->created_at)) ?>
                    <?= ($telegram_message->updated_by !== $telegram_message->created_by) ?
                        Html::tag('p', Yii::t('app', 'Updated At') . ": " . Yii::$app->formatter->asDatetime($telegram_message->updated_at)) :
                        Html::tag('p', Yii::t('app', 'Updated At') . ": ")
                    ?>
                    <?= Html::tag('p', Yii::t('app', 'Elapsed Time') . ": " . Yii::$app->formatter->asRelativeTime($telegram_message->updated_at, $telegram_message->created_at)) ?>
                </div>
            </div>
            <?php
        }
    }
    echo Html::endTag('div');
}
