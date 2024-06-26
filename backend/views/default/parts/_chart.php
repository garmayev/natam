<?php
/**
 * @var \yii\web\View $this
 */

use \common\models\Order;

$orders = Order::find()
	->select(['id', 'COUNT(DAY(FROM_UNIXTIME(`created_at`))) as `count`'])
	->where(['MONTH(FROM_UNIXTIME(`created_at`))' => 'MONTH(NOW())'])
	->groupBy(['DAY(FROM_UNIXTIME(`created_at`))'])
	->all();
$label = $data = [];
foreach ($orders as $order) {
    $label[] = Yii::$app->formatter->asDate($order["created_at"]);
    $data[] = intval($order["count"]);
};
$this->registerJsVar("dataset", $data);
$this->registerJsVar("labels", $label);
?>
<div class="col-md-6">
    <canvas id="myChart" style="height: 350px"></canvas>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {

    })
</script>
