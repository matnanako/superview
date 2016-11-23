# superview

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]


## Install

Via Composer

``` bash
$ composer require xzwh/superview@dev
```

## Usage
Firstly, add "SuperView\Providers\SuperViewModelProvider::class" into the providers array in laravel's config/app.php.
``` php
$superview = new SuperView\SuperView::getInstance($configs);
$superview['soft']->getRecentList(['limit'=>50]);
```

## Configs
```
[
    'api_base_url' => 'http://api.base.url',
    'cache_minutes' => 120, // 通用缓存时间，单位：分
    'info_route_role' => '/{channel}/{id}',
    'category_route_role' => '/{category_id}',
]
```


## Api
### category
#### 1. getInfo($id)
参数:
| 参数名        | 描述                                | 必填  | 默认    |
| ------------- | ----------------------------------- | :---: | :-----: |
| id            | 分类id                              | 是    | null    |
### content(支持使用具体的channel名称)
#### 1. getRecentList($params)
参数
| 参数名        | 描述                                | 必填  | 默认    |
| ------------- | ----------------------------------- | :---: | :-----: |
| params        | 参数数组                            | 否    | null    |
params
| 参数名        | 描述                                | 必填  | 默认    |
| ------------- | ----------------------------------- | :---: | :-----: |
| category_id   | 分类id                              | 否    | 0       |
| page          | 分页数                              | 否    | 1       |
| limit         | 每页数据量                          | 否    | 20      |
| is_pic        | 是否只查询带图片的数据              | 否    | 0       |

#### 2. getRankList($params)
参数:
| 参数名        | 描述                                | 必填  | 默认    |
| ------------- | ----------------------------------- | :---: | :-----: |
| params        | 参数数组                            | 否    | null    |
params:
| 参数名        | 描述                                         | 必填  | 默认    |
| ------------- | -------------------------------------------- | :---: | :-----: |
| category_id   | 分类id                                       | 否    | 0       |
| page          | 分页数                                       | 否    | 1       |
| limit         | 每页数据量                                   | 否    | 20      |
| is_pic        | 是否只查询带图片的数据                       | 否    | 0       |
| period        | 排名周期,'day','week','month','all'          | 否    | 0       |

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.


## Security

If you discover any security related issues, please email huangyukun@njxzwh.com instead of using the issue tracker.


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/xzwh/superview.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/xzwh/superview/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/xzwh/superview.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/xzwh/superview.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/xzwh/superview.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/xzwh/superview
[link-travis]: https://travis-ci.org/xzwh/superview
[link-scrutinizer]: https://scrutinizer-ci.com/g/xzwh/superview/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/xzwh/superview
[link-downloads]: https://packagist.org/packages/xzwh/superview
[link-author]: https://coding.net/u/huangyukun
[link-contributors]: ../../contributors
