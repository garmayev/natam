<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
	    '@webroot' => '@app/web',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
    'modules' => [
	'user' => [
	    'class' => 'dektrium\user\Module',
	    'enableRegistration' => false,
	    'enableConfirmation' => false,
	    'rememberFor' => 9676800,
	    'modelMap' => [
	        'User' => \common\models\User::className(),
	    ],
	],
    ],
];
