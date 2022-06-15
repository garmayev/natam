<?php

use common\models\Order;
use common\models\TelegramMessage;
use kartik\date\DatePicker;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use garmayev\staff\models\Employee;


/**
 * @var $this View
 * @var $models Order[]
 */

$from_date = (isset($_GET['from_date'])) ? Yii::$app->formatter->asTimestamp($_GET['from_date']) : Yii::$app->params['startDate'];
$to_date = (isset($_GET['to_date'])) ? Yii::$app->formatter->asTimestamp($_GET['to_date']) : time();
echo Html::beginForm(Url::to(['analytics/orders']), 'get');
echo DatePicker::widget([
	'name' => 'from_date',
	'value' => Yii::$app->formatter->asDate($from_date, 'php:Y-m-d'),
	'type' => DatePicker::TYPE_RANGE,
	'name2' => 'to_date',
	'value2' => Yii::$app->formatter->asDate($to_date, 'php:Y-m-d'),
	'separator' => '<i class="fas fa-arrows-h"></i>',
	'pluginOptions' => [
		'autoclose' => true,
		'format' => 'yyyy-mm-dd'
	]
]);
echo Html::submitButton('Export', ['class' => ['btn', 'btn-success'], 'name' => 'export', 'value' => 'export']);
echo Html::submitButton('Filter', ['class' => ['btn', 'btn-primary'], 'name' => 'filter', 'value' => 'filter']);
echo Html::endForm();

foreach ($models as $model) {
	if ( $model->messages ) {
	echo Html::a(Html::tag('h4', Yii::t('app', 'Order #{id}', ['id' => $model->id]).Html::tag('em', "({$model->getStatus($model->status)})", ['class' => 'small'])), ['order/view', 'id' => $model->id]);
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
            <div class="panel <?= $class ?>" style="width: 31%; margin: 0 .5%; display: inline-block" data-status="<?= $model->status ?>">
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
                        } else if (isset($temegram_message->created_by)) {
				if ($telegram_message->order_status === Order::STATUS_DELIVERY) {
					$e = Employee::findOne(['chat_id' => $telegram_message->chat_id]);
					echo "<p>Заявка отправлена: {$e->getFullname()}</p>";
				} else {
	                    		echo "<p>Открыл этап: {$telegram_message->createdBy->employee->getFullname()}</p>";
				}
                        } else {
					$e = Employee::findOne(['chat_id' => $telegram_message->chat_id]);
					echo "<p>Заявка отправлена: {$e->getFullname()}</p>";
//				echo "<p>Заказали на сайте</p>";
			}
                    ?>
                    <?= Html::tag('p', Yii::t('app', 'Created At').": ".Yii::$app->formatter->asDatetime($telegram_message->created_at)) ?>
                    <?= ($telegram_message->updated_by !== $telegram_message->created_by) ?
				Html::tag('p', Yii::t('app', 'Updated At').": ".Yii::$app->formatter->asDatetime($telegram_message->updated_at)) :
				Html::tag('p', Yii::t('app', 'Updated At').": ")
		    ?>
                    <?= Html::tag('p', Yii::t('app', 'Elapsed Time').": ".Yii::$app->formatter->asRelativeTime($telegram_message->updated_at, $telegram_message->created_at)) ?>
                </div>
            </div>
			<?php
		}
	}
	echo Html::endTag('div');
    }
}
