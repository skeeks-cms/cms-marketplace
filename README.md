Marketplace for SkeekS CMS
===================================

Installation
------------

The preferred way to install this extension is through [composer](https://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist skeeks/cms-marketplace "*"
```

or add

```
"skeeks/cms-marketplace": "*"
```

Configuration app
----------

```php

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
                'class'             => 'yii\i18n\PhpMessageSource',
                'basePath'          => '@skeeks/cms/marketplace/messages',
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
        'class'         => 'skeeks\cms\marketplace\CmsMarketplaceModule',
    ]
]

```


___

> [![skeeks!](https://skeeks.com/img/logo/logo-no-title-80px.png)](https://skeeks.com)  
<i>SkeekS CMS (Yii2) — quickly, easily and effectively!</i>  
[skeeks.com](https://skeeks.com) | [cms.skeeks.com](https://cms.skeeks.com)

