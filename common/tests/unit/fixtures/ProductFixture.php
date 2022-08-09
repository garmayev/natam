<?php

namespace unit\fixtures;

use common\models\Product;
use yii\test\ActiveFixture;

class ProductFixture extends ActiveFixture
{
    public $modelClass = Product::class;

    public $dataFile = "@tests/_data/product.php";

    public $depends = [
        CategoryFixture::class
    ];
}