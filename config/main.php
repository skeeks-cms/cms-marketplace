<?php
return [

    'components' =>
    [
        'cmsMarkeplace' =>
        [
            'class' => '\skeeks\cms\marketplace\CmsMarketplaceComponent',
        ],
    ],

    'modules' =>
    [
        'cmsMarkeplace' => [
            'class'         => 'skeeks\cms\marketplace\CmsMarketplaceModule',
        ]
    ]
];