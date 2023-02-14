<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'MRUHoTKRk4ZIQR2reOUnWXLNjsBJv_qb',
        ],
    ],
    'modules' => [
	'api' => [
	    'class' => frontend\modules\api\Module::class
	]
    ],
];

if (!YII_ENV_TEST) {
//    // configuration adjustments for 'dev' environment
//    $config['bootstrap'][] = 'debug';
//    $config['modules']['debug'] = [
//        'class' => 'yii\debug\Module',
//    ];
//
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
//	    'allowedIPs' => ['127.0.0.1', '176.214.204.133']
    ];
}

return $config;
