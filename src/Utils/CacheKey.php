<?php

namespace SuperView\Utils;

/**
 * 缓存生成规则和部分关于缓存生成逻辑功能以及缓存读取
 */
class CacheKey
{
    /**
     * 通过反射组合参数生成key
     *
     * @param $method
     * @param $params
     * @param $model
     * @param $virtualModel
     * @return array
     */
    public static function makeCachekey($method, $params, $model, $virtualModel)
    {
        $pivot = ':' . self::confirm_type($virtualModel);
        $pattern = $virtualModel;
        $action = $method;
        //反射获取方法默认参数以及默认值（优先使用传递的参数作为key，没有用默认值）
        $param = self::reflex($model, $method);
        foreach ($param as $k => $value) {
            $depend[$value->getName()] = isset($params[$value->getPosition()]) ? is_array($params[$value->getPosition()]) ? reset($params[$value->getPosition()]) : $params[$value->getPosition()] : $value->getDefaultValue();
        }
        //确保cachekey参数中的第一个为classid
        if (isset($depend)) {
            foreach ($depend as $k => $v) {
                if ($k == 'classid') {
                    $key = $pivot . '::' . $pattern . '::' . $action . '::' . $v;
                }
            }
        } else {
            $depend = [];
        }
        isset($key) ? $key : $key = $pivot . '::' . $pattern . '::' . $action;
        //参数除classid部分，过滤不需要存进缓存的参数，其他参数一一排序组成cachekey
        $key .= self::filterStr($depend);
        $reult['depend'] = $depend;
        $reult['key'] = $key;
        return $reult;
    }

    /**
     * 过滤字段生成最终缓存key
     *
     * @param $depend
     * @return mixed
     */
    public static function filterStr($depend)
    {
        $key = '';
        foreach ($depend as $k => $v) {
            if (!in_array($k, ['limit', 'isPic', 'classid'])) {
                if ($v) {
                    $key .= '::' . (is_array($v) ? reset($v) : $v);
                }
            }
        }
        return $key;
    }

    /**
     * 确认支点
     *
     * @param $virtualModel
     * @return string
     */
    public static function confirm_type($virtualModel)
    {
        $all_types = \Sconfig::get('type');
        foreach ($all_types as $k => $v) {
            if (in_array($virtualModel, $v)) {
                return $k;
            }
        }
        return 'Other';
    }

    /**
     * 反射获取参数
     *
     * @param $model
     * @param $method
     * @return \ReflectionParameter[]
     */
    public static function reflex($model, $method)
    {
        $ReflectionFunc = new \ReflectionMethod($model, $method);
        return $ReflectionFunc->getParameters();
    }

    /**
     * 针对缓存key且返回
     *
     * @param $params
     * @param $model
     * @param $method
     * @param $cacheMinutes
     * @return mixed
     */
    public static function insertCahce($params, $model, $method, $cacheMinutes)
    {
        $cacheKeyInfo = \SCache::getCachekey($model, $method, $params, $cacheMinutes);
        return $cacheKeyInfo['key'];
    }

    /**
     * 定制方法的key生成
     *
     * @param $modelAlias
     * @param $method
     * @param $param
     * @return string
     */
    public static function custom($modelAlias, $method, $param)
    {
        return ':' . self::confirm_type($modelAlias)
        . '::' . $modelAlias . '::'
        . $method
        . (isset($param['classid']) ? '::' . $param['classid'] : '')
        . self::filterStr($param);
    }

    /**
     * 判断缓存是否存在
     *
     * @param $cacheKey
     * @return bool
     */
    public static function haveCache($cacheKey)
    {
        return \SCache::has($cacheKey);
    }

    /**
     * 定制方法生成缓存
     *
     * @param $data
     * @param $allCacheKey
     * @param int $cacheMinutes
     */
    public static function customMakeCache($data, $allCacheKey, $cacheMinutes = 120)
    {
        foreach ($data as $k => $v) {
            \SCache::put($allCacheKey[$k], $v, $cacheMinutes);
        }
    }

    /**
     * 返回缓存结果
     *
     * @param $allCacheKey
     * @return mixed
     *
     */
    public static function getAllCache($allCacheKey)
    {
        $result = array();
        foreach ($allCacheKey as $k => $v) {
            $result[$k] = \SCache::get($v) ?: [];
        }
        return $result;
    }

    /**
     * 获取custom所有参数及方法    最后修改方法名列如(index方法传递给api时需要修改为 lists)
     *
     * @param string $method 方法
     * @param array $arguments ＿＿call所有参数
     * @return mixed
     * @throws \Exception
     */
    public static function customAll($method, $arguments)
    {
        $res['key'] = array_shift($arguments);
        $res['modelAlias'] = array_shift($arguments);
        $methodParam = self::reflexMethod($res['modelAlias'], $method);
        $res['param'] = [];
        foreach ($methodParam AS $parameter) {
            $position = $parameter['position'];
            $res['param'][$parameter['name']] = isset($arguments[$position]) ? $arguments[$position] : $parameter['defaultValue'];
        }
        return $res;
    }


    /**
     * 反射获取参数
     *
     * @param string $modelAlias 模型别名
     * @param string $method 方法名
     * @return \ReflectionParameter[]
     * @throws \Exception
     */
    public static function reflexMethod($modelAlias, $method)
    {
        $key = 'reflex::' . $modelAlias . '::' . $method;
        if (!\SCache::has($key)) {
            $model = self::getModel($modelAlias);
            if (!method_exists($model, $method)) {
                throw new \Exception("调用不存在方法 类:{$model} 方法: {$method}");
            }
            $ReflectionFunc = new \ReflectionMethod($model, $method);
            $methodParam = $ReflectionFunc->getParameters();
            $data = array();
            foreach ($methodParam AS $parameter) {
                $pa['name'] = $parameter->name;
                $pa['position'] = $parameter->getPosition();
                $pa['defaultValue'] = $parameter->isOptional() ? $parameter->getDefaultValue() : '';
                $data[] = $pa;
            }
            \SCache::forever($key, $data);
        }
        return \SCache::get($key);
    }

    /**
     * 返回模型类
     *
     * @param $modelAlias
     * @return mixed
     */
    private static function getModel($modelAlias)
    {
        $models = \SConfig::get('models');
        return array_key_exists($modelAlias, $models) ? $models[$modelAlias] : $models['content'];
    }

    /**
     * 判断复合查询且重组结果（复合查询结果是有多个list的数组['1'=>['list'=>[]],['2'=>['list'=>[]]]]，returnWithPage方法只会返回$data['list'].）
     *
     * @param array $params 请求api的参数
     * @param array $data api返回的结果
     * @return mixed
     */
    public static function isComposite($params, $data)
    {
        if (isset($params['arguments'])) {
            return $data['data'];
        }
        $composite = 0;
        foreach ($params as $v) {
            if (is_array($v) && count($v) > 1) {
                $composite = 1;
                break;
            }
        }
        if ($composite == 1) {
            foreach ($data['data'] as $k => $v) {
                $data['data'][$k] = $v['list'];
            }
        }
        return $data['data'];
    }

    /**
     * content模型且不是info方法的 執行 addListInfo
     *
     * @param array $key 单次请求的所有数据
     * @return int 1指需要执行addlist方法 2不需要走addlist方法 3代表自定义方法需要循环后走adddlist方法
     */
    public static function getModelMethod($key)
    {
        if (self::getModel($key[1]) == 'SuperView\Models\ContentModel' && $key[2] != 'info' && $key[2] != 'count') {
            return 1;
        }
        if (in_array($key[2],['specials', 'topics'])) {
            return 3;
        }
        //由superTopic方法转换 需要走addlistinfo
        if ($key[2] == 'superTopic' || $key[2] == 'taginfo') {
            return 1;
        }
        return 2;
    }

    /**
     * getOnly方法的特殊缓存key生成
     *
     * @param array $params 拼接的请求参数
     * @return string
     */
    public static function getOnlyCacheKey($params)
    {
        return ':getOnly::' . md5(json_encode($params));
    }
}
