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
$ composer require xzwh/superview:~1.0@dev
```

## Usage
``` php
$superview =  SuperView\SuperView::getInstance($configs);
$superview['soft']->recent();
```
  
缓存默认使用全局配置`cache_minutes`, 如果需要为单独的请求设置缓存时间，可以使用cache方法, 参数为分钟。
``` php
$superview['soft']->cache(10)->recent();
```
如果需要修改所有的查询都为设置的缓存时间，可以使用第二个参数，缓存时间将一直保留，直到下一次设置cache.
``` php
$superview['soft']->cache(10, true)->recent();
$superview['soft']->recent(); //仍然使用上面的缓存时间

$superview['soft']->cache(20)->recent(); //使用新的缓存时间，并且只在当前调用中
```
  
## Configs
```
[
    'api_base_url' => 'http://api.base.url',
    'cache_minutes' => 120, // 通用缓存时间，单位：分
    'class_url' => '/{channel}/{classname}/list-{classid}-1.html',
    'info_url' => '/{channel}/{classname}/{classid}/{id}.html',
]
```


## Api
### category

#### 1. info($classid)
获取分类信息

参数:
| 参数名        | 描述                                | 必填  | 默认    |
| ------------- | ----------------------------------- | :---: | :-----: |
| classid       | 分类id                              | 是    | null    |

#### 2. finalChildren($classid)
获取子终极分类

参数:
| 参数名        | 描述                                | 必填  | 默认    |
| ------------- | ----------------------------------- | :---: | :-----: |
| classid       | 分类id                              | 是    | null    |

#### 3. children($classid)
获取下一级子分类

参数:
| 参数名        | 描述                                | 必填  | 默认    |
| ------------- | ----------------------------------- | :---: | :-----: |
| classid       | 分类id                              | 是    | null    |

#### 4. brothers($classid)
获取同级兄弟分类

参数:
| 参数名        | 描述                                | 必填  | 默认    |
| ------------- | ----------------------------------- | :---: | :-----: |
| classid       | 分类id                              | 是    | null    |

#### 5. breadcrumbs($classid)
获取分类的面包屑

参数:
| 参数名        | 描述                                | 必填  | 默认    |
| ------------- | ----------------------------------- | :---: | :-----: |
| classid       | 分类id                              | 是    | null    |


### content(支持使用具体的channel名称)

#### 1. info($id)
获取内容信息

参数:
| 参数名        | 描述                                | 必填  | 默认    |
| ------------- | ----------------------------------- | :---: | :-----: |
| id            | 内容id                              | 是    | null    |

#### 2. recent($classid, $page, $limit, $is_pic)
获取最新内容列表

参数:
| 参数名        | 描述                                | 必填  | 默认    |
| ------------- | ----------------------------------- | :---: | :-----: |
| classid       | 分类id                              | 否    | 0       |
| page          | 分页数                              | 否    | 1       |
| limit         | 每页数据量                          | 否    | 20      |
| is_pic        | 是否只查询带图片的数据              | 否    | 0       |

#### 3. rank($classid, $page, $limit, $is_pic, $period)
获取周期排行列表

参数:
| 参数名        | 描述                                         | 必填  | 默认    |
| ------------- | -------------------------------------------- | :---: | :-----: |
| classid       | 分类id                                       | 否    | 0       |
| page          | 分页数                                       | 否    | 1       |
| limit         | 每页数据量                                   | 否    | 20      |
| is_pic        | 是否只查询带图片的数据                       | 否    | 0       |
| period        | 排名周期,'day','week','month','all'          | 否    | 0       |

#### 4. good($classid, $page, $limit, $is_pic, $level, $order)
获取推荐列表

参数:
| 参数名        | 描述                                         | 必填  | 默认     |
| ------------- | -------------------------------------------- | :---: | :------: |
| classid       | 分类id                                       | 否    | 0        |
| page          | 分页数                                       | 否    | 1        |
| limit         | 每页数据量                                   | 否    | 20       |
| is_pic        | 是否只查询带图片的数据                       | 否    | 0        |
| level         | 置顶等级，0 - 9(0为不置顶)                   | 否    | 0        |
| order         | 排序字段                                     | 否    | newstime |

#### 5. top($classid, $page, $limit, $is_pic, $level, $order)
获取推荐列表

参数:
| 参数名        | 描述                                         | 必填  | 默认     |
| ------------- | -------------------------------------------- | :---: | :------: |
| classid       | 分类id                                       | 否    | 0        |
| page          | 分页数                                       | 否    | 1        |
| limit         | 每页数据量                                   | 否    | 20       |
| is_pic        | 是否只查询带图片的数据                       | 否    | 0        |
| level         | 置顶等级，0 - 9(0为不置顶)                   | 否    | 0        |
| order         | 排序字段                                     | 否    | newstime |

#### 6. firsttitle($classid, $page, $limit, $is_pic, $level, $order)
获取推荐列表

参数:
| 参数名        | 描述                                         | 必填  | 默认     |
| ------------- | -------------------------------------------- | :---: | :------: |
| classid       | 分类id                                       | 否    | 0        |
| page          | 分页数                                       | 否    | 1        |
| limit         | 每页数据量                                   | 否    | 20       |
| is_pic        | 是否只查询带图片的数据                       | 否    | 0        |
| level         | 置顶等级，0 - 9(0为不置顶)                   | 否    | 0        |
| order         | 排序字段                                     | 否    | newstime |

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
