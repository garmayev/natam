<?php

use common\models\Client;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $client Client
 * @var $form ActiveForm
 */

echo $form->field($client, "name");
echo $form->field($client, "phone");
echo $form->field($client, "email");
