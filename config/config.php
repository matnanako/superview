<?php
return [
    'class_url' => '/{channel}/{class_name}/list-{classid}-1.html',
    'info_url' => '/{channel}/{id}.html',

    // Cache lifetime.
    'cache_minutes' => 0,

    // Api service host.
    'api_base_url' => 'http://xzwh.api.zz314.com/downza/',

    // Models alias map to class.
    'models' => [
        'content' => SuperView\Models\ContentModel::class,
        'category' => SuperView\Models\CategoryModel::class,
        'tag' => SuperView\Models\TagModel::class,
        'topic' => SuperView\Models\TopicModel::class,
    ],

    // Default DAL type.
    'default_dal'=> 'api',

    'dals' => [
        'content' => SuperView\Dal\Api\Content::class,
        'category' => SuperView\Dal\Api\Category::class,
        'tag' => SuperView\Dal\Api\Tag::class,
        'topic' => SuperView\Dal\Api\Topic::class,
    ]
];
