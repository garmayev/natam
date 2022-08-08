<?php

namespace unit\fixtures;

use common\models\Client;
use yii\test\ActiveFixture;

class ClientFixture extends ActiveFixture
{
    public $modelClass = Client::class;

    public $dataFile = "@tests/_data/client.php";
}