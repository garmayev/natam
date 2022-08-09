<?php

use common\models\staff\State;
use yii\web\View;



/**
 * @var $this View
 * @var $model State
 */

\yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'title',
        'salary'
    ]
]);