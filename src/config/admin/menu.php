<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 15.04.2016
 */
return [
    'marketplace' => [
        'priority' => 400,
        'label' => \Yii::t('skeeks/marketplace', 'Marketplace'),
        "img" => ['skeeks\cms\marketplace\assets\CmsMarkerplaceAsset', 'icons/marketplace.png'],

        'items' => [
            [
                "label" => \Yii::t('skeeks/marketplace', "Catalog"),
                "url" => ["cmsMarketplace/admin-marketplace/catalog"],
                "img" => ['skeeks\cms\marketplace\assets\CmsMarkerplaceAsset', 'icons/marketplace.png']
            ],

            [
                "label" => \Yii::t('skeeks/marketplace', "Installed"),
                "url" => ["cmsMarketplace/admin-marketplace/index"],
                "img" => ['skeeks\cms\marketplace\assets\CmsMarkerplaceAsset', 'icons/installed.png']
            ],

            [
                "label" => \Yii::t('skeeks/marketplace', "Install{s}Delete", ['s' => '/']),
                "url" => ["cmsMarketplace/admin-marketplace/install"],
                "img" => ['skeeks\cms\marketplace\assets\CmsMarkerplaceAsset', 'icons/installer.png']
            ],

            [
                "label" => \Yii::t('skeeks/marketplace', "Updated platforms"),
                "url" => ["cmsMarketplace/admin-composer-update/update"],
                "img" => ['skeeks\cms\marketplace\assets\CmsMarkerplaceAsset', 'icons/update-2.png']
            ],
        ]
    ],
];