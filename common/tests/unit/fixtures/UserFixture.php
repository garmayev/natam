<?php

namespace unit\fixtures;

use common\models\User;
use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = User::class;

    public $dataFile = "@tests/_data/user.php";
}