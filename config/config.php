<?php
return [
    'categories_config' => 'categories.auto',

    // Cache lifetime.
    'cache_minutes' => 60,

    // Api service host.
    'api_base_url' => 'http://xzwh.api.zz314.com/downza/',

    // The prefix of the service bind into service container.
    'service_prefix' => 'superservice_',
    // The prefix of the model bind into service container.
    'model_prefix' => 'superviewmodel_',

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
        'api' => [
            'content' => SuperView\Dal\Api\Content::class,
            'category' => SuperView\Dal\Api\Category::class,
            'tag' => SuperView\Dal\Api\Tag::class,
            'topic' => SuperView\Dal\Api\Topic::class,
        ],
        'local' => [
            'content' => SuperView\Dal\Local\Content::class,
            'category' => SuperView\Dal\Local\Category::class,
            'tag' => SuperView\Dal\Local\Tag::class,
            'topic' => SuperView\Dal\Local\Topic::class,
        ]
    ]
];