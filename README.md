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
SuperView::setConfig($configs);
SuperView::get('soft')->recent();
```
  
缓存默认使用全局配置`cache_minutes`, 如果需要为单独的请求设置缓存时间，可以使用cache方法, 参数为分钟。
``` php
SuperView::get('soft')->cache(10)->recent();
```
如果需要修改所有的查询都为设置的缓存时间，可以使用第二个参数，缓存时间将一直保留，直到下一次设置cache.
``` php
SuperView::get('soft')->cache(10, true)->recent();
SuperView::get('soft')->recent(); //仍然使用上面的缓存时间

SuperView::get('soft')->cache(20)->recent(); //使用新的缓存时间，并且只在当前调用中
```
  
## Configs
```
[
    'api_base_url' => 'http://api.base.url',
    'cache_minutes' => 120, // 通用缓存时间，单位：分 默认120分钟
    'refresh_cache' => 1, // 刷新所有方法的缓存, 1是，0否，默认0
    'class_url' => '/{channel}/{classname}/{classid}.html', //支持参数列表
    'info_url' => '/{channel}/{classname}/{classid}/{id}.html', //支持参数列表
]
```
  

## Api
### category 分类模块

#### 1. info($classid)
获取分类信息
  
参数:
| 参数名        | 描述                                | 必填  | 默认    |
| ------------- | ----------------------------------- | :---: | :-----: |
| classid       | 分类ID                              | 是    | null    |

#### 2. finalChildren($classid)
获取子终极分类
  
参数:
| 参数名        | 描述                                | 必填  | 默认    |
| ------------- | ----------------------------------- | :---: | :-----: |
| classid       | 分类ID                              | 是    | null    |

#### 3. children($classid)
获取下一级子分类
  
参数:
| 参数名        | 描述                                | 必填  | 默认    |
| ------------- | ----------------------------------- | :---: | :-----: |
| classid       | 分类ID                              | 是    | null    |
  
#### 4. brothers($classid)
获取同级兄弟分类
  
参数:
| 参数名        | 描述                                | 必填  | 默认    |
| ------------- | ----------------------------------- | :---: | :-----: |
| classid       | 分类ID                              | 是    | null    |
  
#### 5. breadcrumbs($classid)
获取分类的面包屑

参数:
| 参数名        | 描述                                | 必填  | 默认    |
| ------------- | ----------------------------------- | :---: | :-----: |
| classid       | 分类ID                              | 是    | null    |
  
  
  
### content 内容模块(支持使用具体的channel名称)
  
#### 1. info($id)
获取内容信息
  
参数:
| 参数名        | 描述                                | 必填  | 默认    |
| ------------- | ----------------------------------- | :---: | :-----: |
| id            | 内容ID                              | 是    | null    |
  
#### 2. recent($classid, $page, $limit, $isPic)
获取最新内容列表
  
参数:
| 参数名        | 描述                                | 必填  | 默认    |
| ------------- | ----------------------------------- | :---: | :-----: |
| classid       | 分类ID                              | 否    | 0       |
| page          | 分页数                              | 否    | 1       |
| limit         | 每页数据量                          | 否    | 20      |
| isPic         | 是否只查询带图片的数据              | 否    | 0       |
  
#### 3. rank($classid, $page, $limit, $isPic, $period)
获取周期排行列表
  
参数:
| 参数名        | 描述                                         | 必填  | 默认    |
| ------------- | -------------------------------------------- | :---: | :-----: |
| classid       | 分类ID                                       | 否    | 0       |
| page          | 分页数                                       | 否    | 1       |
| limit         | 每页数据量                                   | 否    | 20      |
| isPic         | 是否只查询带图片的数据                       | 否    | 0       |
| period        | 排名周期,'day','week','month','all'          | 否    | 0       |
  
#### 4. good($classid, $page, $limit, $isPic, $level, $order)
获取推荐列表
  
参数:
| 参数名        | 描述                                         | 必填  | 默认     |
| ------------- | -------------------------------------------- | :---: | :------: |
| classid       | 分类ID                                       | 否    | 0        |
| page          | 分页数                                       | 否    | 1        |
| limit         | 每页数据量                                   | 否    | 20       |
| isPic         | 是否只查询带图片的数据                       | 否    | 0        |
| level         | 置顶等级，0 - 9(0为不置顶)                   | 否    | 0        |
| order         | 排序字段                                     | 否    | newstime |
  
#### 5. top($classid, $page, $limit, $isPic, $level, $order)
获取置顶列表
  
参数:
| 参数名        | 描述                                         | 必填  | 默认     |
| ------------- | -------------------------------------------- | :---: | :------: |
| classid       | 分类ID                                       | 否    | 0        |
| page          | 分页数                                       | 否    | 1        |
| limit         | 每页数据量                                   | 否    | 20       |
| isPic         | 是否只查询带图片的数据                       | 否    | 0        |
| level         | 置顶等级，0 - 9(0为不置顶)                   | 否    | 0        |
| order         | 排序字段                                     | 否    | newstime |
  
#### 6. firsttitle($classid, $page, $limit, $isPic, $level, $order)
获取头条列表
  
参数:
| 参数名        | 描述                                         | 必填  | 默认     |
| ------------- | -------------------------------------------- | :---: | :------: |
| classid       | 分类ID                                       | 否    | 0        |
| page          | 分页数                                       | 否    | 1        |
| limit         | 每页数据量                                   | 否    | 20       |
| isPic         | 是否只查询带图片的数据                       | 否    | 0        |
| level         | 置顶等级，0 - 9(0为不置顶)                   | 否    | 0        |
| order         | 排序字段                                     | 否    | newstime |
  
#### 7. today($classid, $page, $limit, $isPic, $order)
获取今日列表
  
参数:
| 参数名        | 描述                                         | 必填  | 默认     |
| ------------- | -------------------------------------------- | :---: | :------: |
| classid       | 分类ID                                       | 否    | 0        |
| page          | 分页数                                       | 否    | 1        |
| limit         | 每页数据量                                   | 否    | 20       |
| isPic         | 是否只查询带图片的数据                       | 否    | 0        |
| order         | 排序字段                                     | 否    | newstime |
  
#### 8. interval($startTime, $endTime, $classid, $page, $limit, $isPic, $order)
获取时间段列表
  
参数:
| 参数名        | 描述                                         | 必填  | 默认     |
| ------------- | -------------------------------------------- | :---: | :------: |
| startTime     | 开始时间（时间戳）                           | 否    | 0        |
| endTime       | 结束时间（时间戳）                           | 否    | 0        |
| classid       | 分类ID                                       | 否    | 0        |
| page          | 分页数                                       | 否    | 1        |
| limit         | 每页数据量                                   | 否    | 20       |
| isPic         | 是否只查询带图片的数据                       | 否    | 0        |
| order         | 排序字段                                     | 否    | newstime |
  
#### 9. title($title, $classid, $page, $limit, $isPic, $order)
获取相同名称内容列表
  
参数:
| 参数名        | 描述                                         | 必填  | 默认     |
| ------------- | -------------------------------------------- | :---: | :------: |
| title         | 内容标题                                     | 是    | null     |
| classid       | 分类ID                                       | 否    | 0        |
| page          | 分页数                                       | 否    | 1        |
| limit         | 每页数据量                                   | 否    | 20       |
| isPic         | 是否只查询带图片的数据                       | 否    | 0        |
| order         | 排序字段                                     | 否    | newstime |
  
#### 10. related($id, $classid, $page, $limit, $isPic, $order)
获取内容相关内容列表
  
参数:
| 参数名        | 描述                                         | 必填  | 默认     |
| ------------- | -------------------------------------------- | :---: | :------: |
| id            | 内容ID                                       | 是    | null     |
| page          | 分页数                                       | 否    | 1        |
| limit         | 每页数据量                                   | 否    | 20       |
| isPic         | 是否只查询带图片的数据                       | 否    | 0        |
| order         | 排序字段                                     | 否    | newstime |
  
#### 11. tag($tag, $classid, $page, $limit, $isPic, $order)
获取tag相关内容列表
  
参数:
| 参数名        | 描述                                         | 必填  | 默认     |
| ------------- | -------------------------------------------- | :---: | :------: |
| tag           | tag标题                                      | 是    | null     |
| page          | 分页数                                       | 否    | 1        |
| limit         | 每页数据量                                   | 否    | 20       |
| isPic         | 是否只查询带图片的数据                       | 否    | 0        |
| order         | 排序字段                                     | 否    | newstime |
  
#### 12. infoTopics($id, $limit)
获取信息所属专题列表
  
参数:
| 参数名        | 描述                                         | 必填  | 默认     |
| ------------- | -------------------------------------------- | :---: | :------: |
| id            | 内容ID                                       | 是    | null     |
| limit         | 每页数据量                                   | 否    | 20       |
  
#### 13. topic($topicId, $page, $limit)
获取专题信息列表
  
参数:
| 参数名        | 描述                                         | 必填  | 默认     |
| ------------- | -------------------------------------------- | :---: | :------: |
| topicId       | 专题ID                                       | 是    | null     |
| page          | 分页数                                       | 否    | 1        |
| limit         | 每页数据量                                   | 否    | 20       |
  
#### 14. search($keyword, $classid, $page, $limit, $isPic, $order)
获取tag相关内容列表
  
参数:
| 参数名        | 描述                                         | 必填  | 默认     |
| ------------- | -------------------------------------------- | :---: | :------: |
| keyword       | 关键词                                       | 是    | null     |
| classid       | 分类ID                                       | 否    | 0        |
| page          | 分页数                                       | 否    | 1        |
| limit         | 每页数据量                                   | 否    | 20       |
| isPic         | 是否只查询带图片的数据                       | 否    | 0        |
| order         | 排序字段                                     | 否    | newstime |
  
### topic 专题模块
  
#### 1. all($classid, $topicCategoryId, $page, $limit, $order)
获取专题列表
  
参数:
| 参数名          | 描述                                         | 必填  | 默认     |
| --------------- | -------------------------------------------- | :---: | :------: |
| classid         | 分类ID                                       | 否    | 0        |
| topicCategryId  | 专题分类ID                                   | 否    | 0        |
| page            | 分页数                                       | 否    | 1        |
| limit           | 每页数据量                                   | 否    | 20       |
| order           | 排序字段                                     | 否    | addtime  |
  
#### 2. info($id, $path)
获取专题信息
  
参数:
| 参数名          | 描述                                         | 必填  | 默认     |
| --------------- | -------------------------------------------- | :---: | :------: |
| id              | 专题ID                                       | 是    | null     |
| path            | 专题路径,例如：/zt/qq                        | 否    | ''       |
  
#### 3. categories()
获取所有专题分类列表
  

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
