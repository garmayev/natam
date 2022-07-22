<?php
return [
    'id' => 'app-backend-tests',
    'basePath' => dirname(__DIR__),
    'components' => [
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'common\models\User',
        ],
    ],
];
