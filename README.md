Приватный репозиторий git@amgcompany.ru:natam.git

Контакты для Геотаргетинга:

    Максим - 8(964)405-72-87
    Виктория - 8(924)775-50-00

Геотаргетинг

    https://kb.scout-gps.ru/category/%d0%a1%d1%80%d0%b5%d0%b4%d1%81%d1%82%d0%b2%d0%b0-%d0%b8%d0%bd%d1%82%d0%b5%d0%b3%d1%80%d0%b0%d1%86%d0%b8%d0%b8/%d0%a1%d0%9f%d0%98%d0%9a/197/

Установка:
    
1. Установка расширения для распределения прав и ролей

```composer require --prefer-dist yii2mod/yii2-rbac "*"```

2. Миграции для AuthManager

```./yii migrate/up --migrationPath=@yii/rbac/migrations```

3. Добавить в настройки приложения:

```
    'modules' => [
        'rbac' => [
            'class' => 'yii2mod\rbac\Module',
        ],
    ],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest', 'user'],
        ],
        'i18n' => [
            'translations' => [
                'yii2mod.rbac' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@yii2mod/rbac/messages',
                ],
            ],
        ],
    ],
```

4. Консольное приложение

```
'modules' => [
    'rbac' => [
        'class' => 'yii2mod\rbac\ConsoleModule'
    ]
]
```