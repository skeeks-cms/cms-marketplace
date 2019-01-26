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
    ],
];