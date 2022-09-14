<?php

namespace backend\modules\api\controllers;

use common\models\Company;

class CompanyController extends \yii\rest\ActiveController
{
    public $modelClass = Company::class;
}