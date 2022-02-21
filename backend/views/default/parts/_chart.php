<?php
/**
 * @var \yii\web\View $this
 */
$orders = Yii::$app->db
	->createCommand("select *, COUNT(DAY(FROM_UNIXTIME(`created_at`))) as `count` from `order` where MONTH(FROM_UNIXTIME(`created_at`)) = (MONTH(NOW())) group by DAY(FROM_UNIXTIME(`created_at`))")->queryAll();
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
