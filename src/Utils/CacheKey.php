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
     * @param $isVirtualModel    是否多个model，定制化参数  M->get('soft',ture)->top()
     * @return array
     */
    public static function makeCachekey($method, $params, $model, $virtualModel ,$isVirtualModel = false)
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
        if(isset($depend)) {
            foreach ($depend as $k => $v) {
                if ($k == 'classid') {
                    if ($isVirtualModel == true) {
                        $patterns = config('model.' . $virtualModel);
                        $pattern = array_flip($patterns)[$v];
                    }
                    $key = $pivot . '::' . $pattern . '::' . $action . '::' . $v;
                }
            }
        }else{
            $depend=[];
        }
        isset($key) ? $key : $key = $pivot . '::' . $pattern . '::' . $action;
        //参数除classid部分，过滤不需要存进缓存的参数，其他参数一一排序组成cachekey
        $key.=  self::filterStr($depend);
        $reult['depend'] = $depend;
        $reult['key']  = $key;
        return $reult;
    }

    /**
     * 过滤字段生成最终缓存key
     *
     * @param $depend
     * @param $key
     * @return mixed
     */
    public static function filterStr($depend){
        $key='';
        foreach($depend as $k=>$v){
            if(!in_array($k,['limit','isPic','classid'])){
                if($v) {
                     $key.='::' .(is_array($v)?reset($v):$v);
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
    public  static function confirm_type($virtualModel){
        $all_types = \Sconfig::get('type');
        foreach($all_types as $k =>$v){
            if(in_array($virtualModel,$v)){
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
    public static  function reflex($model, $method){
        $ReflectionFunc = new \ReflectionMethod($model, $method);
        return $ReflectionFunc->getParameters();
    }

    /**
     * 根据实际传入参数先拆分数组并生成缓存key插入数组
     *
     * @param $params
     * @param $model
     * @param $method
     * @param $cacheMinutes
     * @return mixed
     */
    public static function insertCahce($params, $model, $method, $cacheMinutes){
        foreach($params as $k=>$v){
            if(is_array($v) && count($v)>1){
                $result['long']=$k;
                foreach($v as $ke=>$ve){
                    $result['detail'][$ke]=$params;
                    $result['detail'][$ke][$k]=[$ve];
                    $result['detail'][$ke]['cacheKey'] = \SCache::getCacheKey($model, $method, $result['detail'][$ke], $cacheMinutes);
                    $result['detail'][$ke]['depend']  = $result['detail'][$ke]['cacheKey']['depend'];
                    $result['detail'][$ke]['cacheKey'] =$result['detail'][$ke]['cacheKey']['key'];

                }
            }
        }
        if(!isset($result['long'])){
            $result['detail'][0]=$params;
            $result['detail'][0]['cacheKey'] = \SCache::getCacheKey($model, $method, $params, $cacheMinutes);
            $result['detail'][0]['depend'] = $result['detail'][0]['cacheKey']['depend'];
            $result['detail'][0]['cacheKey'] = $result['detail'][0]['cacheKey']['key'];
        }//dd($result);
        return self::assemble($result, $params);
    }
    public static function assemble($result, $params){
        //将没有缓存的数据组合！new_arr区分是否有未缓存的数据。params为默认传递过来去除已有缓存和不需要缓存的的新值，用于统一传递api请求
        foreach($result['detail'] as $key=>$cacheKey){
            if(!\SCache::has($cacheKey['cacheKey'])){
                if(isset($result['long'])){
                    $new_arr[]=reset($cacheKey[$result['long']]);
                    $result['noCacheArr'][reset($cacheKey[$result['long']])]=$cacheKey['cacheKey'];
                }else{
                    $new_arr[]=$params;
                }
            }

        }
        //是数组且存在没有缓存的情况
        if(isset($result['long']) && isset($new_arr)) {
            $params[$result['long']] = $new_arr;
        }
        if(isset($new_arr)) {
            $result['new_arr'] = $new_arr;
        }
        $result['params']=$params;
        return $result;
    }

    /**
     * 生成缓存
     *
     * @param $result
     * @param $res
     * @param $cacheMinutes
     */
    public static function makeCache($result, $res ,$cacheMinutes){
        if(isset($res['new_arr'])){
            if(isset($res['long']) && count($res['noCacheArr'])>1){
                foreach($result as $k => $v){
                    Cache::put($res['noCacheArr'][$k], $v, $cacheMinutes);
                }
            }elseif(isset($res['long']) && count($res['noCacheArr'])==1){
                //防止数组中仅存在一个没有缓存的时候，此时请求接口传递的为单个数组如[1] ，此时接口返回的结果不包含cid为下标的返回
                Cache::put(current($res['noCacheArr']), $result, $cacheMinutes);
            }else{
                //针对未修改的方法（如superTopic）直接返回的list的值 故加判断。
                 Cache::put(current($res['detail'])['cacheKey'], $result, $cacheMinutes);
            }
        }
    }

    /**
     * 读取缓存
     *
     * @param $arr
     * @return mixed
     */
    public static function getCache($arr){
        if(count($arr['detail'])==1){
           $limit=self::getLimit($arr['detail'][0]['depend']);
            if(is_array(Cache::get($arr['detail'][0]['cacheKey']))){
                 $data = array_slice(Cache::get($arr['detail'][0]['cacheKey']), 0, $limit);
            }else{
                $data = Cache::get($arr['detail'][0]['cacheKey']);
            }
        }else {
            foreach ($arr['detail'] as $k => $v) {
                $classid = is_array($arr['detail'][$k][$arr['long']]) ? $arr['detail'][$k][$arr['long']][0] : $arr['detail'][$k][$arr['long']];
                $limit=self::getLimit($v['depend']);
                if(is_array(Cache::get($v['cacheKey']))){
                     $data[$classid] = array_slice(Cache::get($v['cacheKey']), 0, $limit);
                 }else{
                    $data[$classid] = Cache::get($v['cacheKey']);
                }
            }
        }
        return $data;
    }
    public static  function getLimit($depend){
        foreach($depend as $k => $v){
            if($k =='limit'){
                return $v==0?100:$v;
            }
        }
        return 100;
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
    public static function haveCache($cacheKey){
        if(\SCache::has($cacheKey)) {
                return true;
        }
        return false;
    }

    /**
     * 定制方法生成缓存
     *
     * @param $data
     * @param $allCacheKey
     * @param int $cacheMinutes
     */
    public static function customMakeCache($data, $allCacheKey, $cacheMinutes=120){
       foreach($data as $k => $v){
           Cache::put($allCacheKey[$k], $v, $cacheMinutes);
       }
    }

    /**
     * 返回缓存结果
     *
     * @param $allCacheKey
     * @return mixed
     *
     */
    public static function getAllCache($allCacheKey){
        foreach($allCacheKey as $k=>$v){
            $result[$k] = Cache::get($v);
        }
        return $result;
    }

    /**
     * 获取custom所有参数及方法    最后修改方法名列如(index方法传递给api时需要修改为 lists)
     *
     * @param $method   方法
     * @param $arguments  ＿＿call所有参数
     * @return mixed
     * @throws \Exception
     */
    public static function customAll($method, $arguments){
        $res['key'] = array_shift($arguments);
        $res['real']['modelAlias'] = $res['modelAlias'] = array_shift($arguments);
        $res['model'] = self::getModel($res['modelAlias']);
        if (!method_exists($res['model'], $method)){
            throw new \Exception("调用不存在方法 类:{$res['model']} 方法: {$method}");
        }
        $methodParam = self::reflex($res['model'], $method);
        $res['param'] = [];
        foreach ($methodParam AS $parameter){
            $position = $parameter->getPosition();
            $res['param'][$parameter->name] = isset($arguments[$position]) ? $arguments[$position] : $parameter->getDefaultValue();
        }
        $res['method'] = $method;
        //特殊方法特殊处理
        if($res['method'] == 'superTopic'){
            $res['modelAlias'] = 'topic';
        }
        return $res;
    }
    /**
     * 返回模型类
     *
     * @param $modelAlias
     * @return mixed
     */
    private static function getModel($modelAlias)
    {
        $models = Config::get('models');
        if (array_key_exists($modelAlias, $models)) {
            $model = $models[$modelAlias];
        } else {
            $model = $models['content'];
        }

        return $model;
    }

    /**
     * 判断复合查询且重组结果（复合查询结果是有多个list的数组['1'=>['list'=>[]],['2'=>['list'=>[]]]]，returnWithPage方法只会返回$data['list'].）
     *
     * @param $params   请求api的参数
     * @param $data     api返回的结果
     * @return mixed
     */
    public static function isComposite($params, $data)
    {
        $composite = 0;
        if(isset($params['arguments'])){
            return $data['data'];
        }
        foreach($params as $v){
            if(is_array($v) && count($v)>1){
                $composite=1;
                break;
            }
        }
        if($composite==1) {
            foreach ($data['data'] as $k => $v) {
                $data['data'][$k] = $v['list'];
            }
        }
          return $data['data'];
    }

    /**
     * content模型 且 不是info方法的 執行 addListInfo   1指需要执行addlist方法   3代表自定义方法需要循环后走adddlist方法  2不需要走addlist方法
     *
     * @param $key    单次请求的所有数据  $this->arguments['key']   array
     * @return bool
     */
    public static function getModelMethod($key)
    {
        if(self::getModel($key[1])=='SuperView\Models\ContentModel' && $key[2]!='info' && $key[2]!='count'){
            return 1;
        }
        if($key[2] == 'specials'){
            return 3;
        }
        //由superTopic方法转换 需要走addlistinfo
        if($key[2] == 'superTopic'){
            return 1;
        }
        return 2;
    }

    /**
     * getOnly方法的特殊缓存key生成
     *
     * @param $params  拼接的请求参数
     * @return string
     */
    public static function getOnlyCacheKey($params)
    {
            return ':getOnly::'.md5(json_encode($params));
    }
}
