<?php

namespace unit\fixtures;

use common\models\Location;
use yii\test\ActiveFixture;

class LocationFixture extends ActiveFixture
{
    public $modelClass = Location::class;

    public $dataFile = "@tests/_data/location.php";
}