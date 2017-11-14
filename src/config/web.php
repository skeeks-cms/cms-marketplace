<?php
return [

    'components' =>
        [
            'cmsMarketplace' =>
                [
                    'class' => '\skeeks\cms\marketplace\CmsMarketplaceComponent',
                ],

            'i18n' => [
                'translations' =>
                    [
                        'skeeks/marketplace' => [
                            'class' => 'yii\i18n\PhpMessageSource',
                            'basePath' => '@skeeks/cms/marketplace/messages',
                            'fileMap' => [
                                'skeeks/marketplace' => 'main.php',
                            ],
                        ]
                    ]
            ],
        ],

    'modules' =>
        [
            'cmsMarketplace' => [
                'class' => 'skeeks\cms\marketplace\CmsMarketplaceModule',
            ]
        ]
];