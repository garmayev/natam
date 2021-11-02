<?php

use frontend\models\Order;
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
		<p>Адрес доставки: <?= $model->address ?></p>
		<p>Текущий статус: <?= $model->getStatus(($model->status === null) ? 0 : $model->status) ?></p>
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
            ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel">
    <div class="panel-heading">
        Сроки выполнения заказа
    </div>
    <div class="panel-body">

            <?php
                $result = [0, 0, 0, 0, 0];
                $updates = \frontend\models\Updates::find()->where(["order_id" => $model->id])->orderBy(["staff_id" => SORT_ASC])->all();
                foreach ( $updates as $update ) {
		    if ( $update->per_time !== null )
                    $result[$update->staff_id] += $update->per_time;
                }

                if ( isset($result[0]) ) {
                    echo Html::tag("p", "Менеджер: {$result[0]} сек");
                }
                if ( isset($result[1]) ) {
                    echo Html::tag("p", "Кладовщик: {$result[1]} сек");
                }
                if ( isset($result[2]) ) {
                    echo Html::tag("p", "Водитель: {$result[2]} сек");
                }
            ?>
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