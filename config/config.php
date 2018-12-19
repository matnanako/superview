<?php
return [
    'class_url' => '',
    'info_url' => '',

    // Cache lifetime.
    'cache_minutes' => 2,
    // 是否刷新缓存.
    'refresh_cache' => 0,

    // Api service host.
    'api_base_url' => 'http://xzwh.api.zz314.com/downza',

    // Models alias map to class.
    'models' => [
        'content' => SuperView\Models\ContentModel::class,
        'category' => SuperView\Models\CategoryModel::class,
        'topic' => SuperView\Models\TopicModel::class,
        'tag' => SuperView\Models\TagModel::class,
        'utils' => SuperView\Models\UtilsModel::class,
        'chip' => SuperView\Models\ChipModel::class,
        'banner' => SuperView\Models\BannerModel::class,
        'custom' => SuperView\Models\CustomModel::class,
    ],

    'dals' => [
        'content' => SuperView\Dal\Api\Content::class,
        'category' => SuperView\Dal\Api\Category::class,
        'topic' => SuperView\Dal\Api\Topic::class,
        'tag' => SuperView\Dal\Api\Tag::class,
        'utils' => SuperView\Dal\Api\Utils::class,
        'chip' => SuperView\Dal\Api\Chip::class,
        'banner' => SuperView\Dal\Api\Banner::class,
        'custom' => SuperView\Dal\Api\Custom::class,
    ],

    'pagination' => [
        'layout' => '',
        'total' => '',
        'previous' => '',
        'links' => '',
        'link_active' => '',
        'next' => '',
        'dots' => '',
    ],

    //新缓存规则部分是使用
    'type' => [
        'category' => ['children','utils','banner','category'],
        'soft' => ['soft','android','iphone','tv','ipad','mac','bt','mobile','content','mgame'],
        'article' => ['xueyuan','edu','news'],
        'zt' => ['topic'],
        'bk' => ['softbaike','provider','tag']
    ],

    //方法名称替换
    'method' => [
        'index' => 'lists',

        'superTopic' => 'infolist',

        'infoTopics'=> 'speciallist',

        'categories'=> 'classlist',
    ]
];