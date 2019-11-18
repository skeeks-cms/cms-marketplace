<?php
return [
    'components' => [
        'cmsMarketplace' => [
            'class' => '\skeeks\cms\marketplace\CmsMarketplaceComponent',
        ],

        'backendAdmin' => [
            'menu' => [
                'data' => [
                    'marketplace' => [
                        'priority' => 400,
                        'name'    => ['skeeks/marketplace', 'Marketplace'],
                        "image"      => ['skeeks\cms\marketplace\assets\CmsMarkerplaceAsset', 'icons/marketplace.png'],

                        'items' => [
                            [
                                "name" => ['skeeks/marketplace', "Catalog"],
                                "url"   => ["cmsMarketplace/admin-marketplace/catalog"],
                                "image"   => ['skeeks\cms\marketplace\assets\CmsMarkerplaceAsset', 'icons/marketplace.png'],
                            ],

                            [
                                "name" => ['skeeks/marketplace', "Installed"],
                                "url"   => ["cmsMarketplace/admin-marketplace/index"],
                                "image"   => ['skeeks\cms\marketplace\assets\CmsMarkerplaceAsset', 'icons/installed.png'],
                            ],

                            [
                                "name" => ['skeeks/marketplace', "Install{s}Delete", ['s' => '/']],
                                "url"   => ["cmsMarketplace/admin-marketplace/install"],
                                "image"   => ['skeeks\cms\marketplace\assets\CmsMarkerplaceAsset', 'icons/installer.png'],
                            ],

                            [
                                "name" => ['skeeks/marketplace', "Updated platforms"],
                                "url"   => ["cmsMarketplace/admin-composer-update/update"],
                                "image"   => ['skeeks\cms\marketplace\assets\CmsMarkerplaceAsset', 'icons/update-2.png'],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'modules' => [
        'cmsMarketplace' => [
            'class' => 'skeeks\cms\marketplace\CmsMarketplaceModule',
        ],
    ],
];