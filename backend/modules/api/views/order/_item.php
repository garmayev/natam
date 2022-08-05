<?php

use common\models\Order;

/**
 * @var $model Order
 */

?>
<Order>
	<parameter>
        <numberDate><?= Yii::$app->formatter->asDate($model->created_at, "php:d-m-Y") ?></numberDate>
    </parameter>
</Order>