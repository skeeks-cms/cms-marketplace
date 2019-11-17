<?php
return [
    'components' => [
        'i18n' => [
            'translations' => [
                'skeeks/marketplace' => [
                    'class'    => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@skeeks/cms/marketplace/messages',
                    'fileMap'  => [
                        'skeeks/marketplace' => 'main.php',
                    ],
                ],
            ],
        ],

        'authManager' => [
            'config' => [
                'roles' => [
                    [
                        'name'  => \skeeks\cms\rbac\CmsManager::ROLE_ADMIN,
                        'child' => [
                            //Есть доступ к системе администрирования
                            'permissions' => [
                                "cmsMarketplace/admin-composer-update",
                                "cmsMarketplace/admin-marketplace",
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];