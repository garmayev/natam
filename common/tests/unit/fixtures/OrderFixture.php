<?php

namespace unit\fixtures;

use common\models\Order;

class OrderFixture extends \yii\test\ActiveFixture
{
    public $modelClass = Order::class;

    public $dataFile = '@tests/_data/order.php';

    public $depends = [
        LocationFixture::class,
        ProductFixture::class,
        ClientFixture::class,
    ];
}