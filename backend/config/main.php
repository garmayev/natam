<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
	'name' => Yii::t('app', 'Natam Trade'),
    'bootstrap' => ['log'],
	'defaultRoute' => 'default/index',
    'components' => [
	    'i18n' => [
		    'translations' => [
			    'yii2mod.rbac' => [
				    'class' => 'yii\i18n\PhpMessageSource',
				    'basePath' => '@yii2mod/rbac/messages',
			    ],
		    ],
	    ],
	    'request' => [
            'csrfParam' => '_csrf-frontend',
	        'baseUrl' => '/admin',
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
	    'authManager' => [
		    'class' => 'yii\rbac\DbManager',
		    'defaultRoles' => ['guest', 'user'],
	    ],
	    'view' => [
		    'theme' => [
			    'pathMap' => [
				    '@dektrium/user/views' => '@app/views/user',
				    '@yii2mod/rbac/views' => '@app/views/rbac',
			    ],
		    ],
	    ],
    ],
	'modules' => [
		'user' => [
			'class' => 'dektrium\user\Module',
			'modelMap' => [
				'User' => \common\models\User::className(),
			],
		],
		'rbac' => [
			'class' => \yii2mod\rbac\Module::class,
		]
	],
    'params' => $params,
];
