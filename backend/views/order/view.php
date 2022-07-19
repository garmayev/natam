<?php

use common\models\Client;
use common\models\HistoryEvent;
use common\models\Order;
use common\models\TelegramMessage;
use common\models\Settings;
use garmayev\staff\models\Employee;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\helpers\Html;


/**
 * @var $this View
 * @var $model Order
 */

$this->title = Yii::t("app", "Order content");

echo Html::a("Изменить заказ", ["order/update", "id" => $model->id], ["class" => ["btn", "btn-success"], "style" => "margin: 15px 0;"]);
$totalCost = 0;
?>
    <div class="panel">
        <div class="panel-heading panel-default">
			<?= Yii::t("app", "Information about Client") ?>
        </div>
        <div class="panel-body">
            <p>ФИО Клиента: <?= $model->client->name ?></p>
            <p>Номер телефона Клиента: <?= $model->client->phone ?></p>
        </div>
    </div>

    <div class="panel">
        <div class="panel-heading panel-success">
			<?= Yii::t("app", "Information about Order") ?>
        </div>
        <div class="panel-body">
			<?php
			if ($model->delivery_type !== Order::DELIVERY_SELF) {
				?>
                <p>Адрес доставки: <?= isset($model->address) ? $model->address : $model->location->title ?></p>
				<?php
			}
			?>
            <p>Текущий статус: <?= $model->getStatus(($model->status === null) ? 0 : $model->status) ?></p>
            <p>Дата доставки: <?= Yii::$app->formatter->asDatetime($model->delivery_date, "php:d M Y H:i") ?></p>
			<?php
			$delivery_price = intval($model->delivery_distance) * Settings::getDeliveryCost();
			if ($model->delivery_distance) {
				echo Html::tag("p", "Стоимость доставки: " . Yii::$app->formatter->asCurrency($delivery_price));
			}
			?>
            <div><p>Комментарий: </p><?= $model->comment ?></div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-heading">
			<?= Yii::t("app", "Order content") ?>
        </div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                <td><?= Yii::t("app", "Title") ?></td>
                <td><?= Yii::t("app", "Value") ?></td>
                <td><?= Yii::t("app", "Price") ?></td>
                <td><?= Yii::t("app", "Count") ?></td>
                <td><?= Yii::t("app", "Cost") ?></td>
                </thead>
                <tbody>
				<?php
				foreach ($model->products as $product) {
					$cost = $product->price * $model->getCount($product->id);
					$totalCost += $cost;
					echo Html::beginTag("tr");
					echo Html::tag("td", $product->title);
					echo Html::tag("td", $product->value);
					echo Html::tag("td", $product->price);
					echo Html::tag("td", $model->getCount($product->id));
					echo Html::tag("td", $cost);
					echo Html::endTag("tr");
				}
				if ($model->delivery_distance) {
                    $totalCost += $delivery_price;
					echo Html::beginTag("tr");
					echo Html::tag("td", "Доставка за пределы города", ['colspan' => 2]);
					echo Html::tag("td", Settings::getDeliveryCost());
					echo Html::tag("td", intval($model->delivery_distance));
					echo Html::tag("td", $delivery_price);
					echo Html::endTag("tr");
				}
				?>
                <tr></tr>
                </tbody>
                <tfoot style="font-size: 18px;">
                <td colspan="4"><b>Общая стоимость</b></td>
                <td><b><?= $totalCost ?></b></td>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="panel">
        <div class="panel-heading">
            Сроки выполнения заказа
        </div>
        <div class="panel-body">
            <table class="table">
                <thead>

                </thead>
                <tbody>
				<?php
				$query = TelegramMessage::find();
				$query
					->filterWhere(['order_id' => $model->id])
					->groupBy('order_status');
				$messages = $query->all();
				$created_by = Client::findOne($model->client_id);
				if (empty($created_by)) {
					$created_by = Employee::findOne($model->client_id);
				}
				echo "
<tr>
<td>" . Order::getStatusList()[1] . "</td>
<td>" . $created_by->getFullname() . "</td>
<td>" . Yii::$app->formatter->asDatetime($model->created_at) . "</td>
</tr>";
            foreach ($messages as $message) {
		$employee = Employee::findOne(["user_id" => $message->updatedBy]);
		$name = isset($employee) ? $employee->fullname : "";
                echo "
<tr>
<td>".Order::getStatusList()[$message->order_status + 1]."</td>
<td>".$name."</td>
<td>".Yii::$app->formatter->asDuration($message->updated_at - $message->created_at)."</td>
</tr>";
				}
				?>
                </tbody>
            </table>
        </div>
    </div>
<?php
echo Html::tag("h2", "Итого цена заказа: $totalCost");
$this->registerJs("
$(document).on('click', '.panel-heading', function(e){
    var that = $(this);
	if(!that.hasClass('panel-collapsed')) {
		that.parents('.panel').find('.panel-body').slideUp();
		that.addClass('panel-collapsed');
		that.find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
	} else {
		that.parents('.panel').find('.panel-body').slideDown();
		that.removeClass('panel-collapsed');
		that.find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
	}
})
", View::POS_LOAD);
$this->registerCss("
.panel-heading {
    cursor: pointer;
}
");