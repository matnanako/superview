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
     * @param $isVirtualModel    是否多个model，定制化参数
     * @return array
     */
    public static function makeCachekey($method, $params, $model, $virtualModel ,$isVirtualModel = false)
    {
        $pivot = ':' . self::confirm_type($virtualModel);
        $pattern = $virtualModel;
        $action = $method;
        //反射获取方法默认参数以及默认值
        $param = self::reflex($model, $method);
        foreach ($param as $k => $value) {
            $depend[$value->getName()] = isset($params[$value->getPosition()]) ? is_array($params[$value->getPosition()]) ? reset($params[$value->getPosition()]) : $params[$value->getPosition()] : $value->getDefaultValue();
        }
        //设置缓存值(从前往后，将实际参数插入默认参数中，然后classid排在第一个，然后去除limit，ispic，其余参数按顺序排列)
        foreach ($depend as $k => $v) {
            if ($k == 'classid') {
                if ($isVirtualModel == true) {
                    $patterns = config('model.' . $virtualModel);
                    $pattern = array_flip($patterns)[$v];
                }
                $key = $pivot . '::' . $pattern . '::' . $action . '::' . $v;
            }
        }
        isset($key) ? $key : $key = $pivot . '::' . $pattern . '::' . $action;
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
        $type=$virtualModel;
        if(in_array($type,$all_types['soft'])){
            return 'soft';
        }
        if(in_array($type,$all_types['category'])){
            return 'category';
        }
        if(in_array($type,$all_types['article'])){
            return 'article';
        }
        if(in_array($type,$all_types['zt'])){
            return 'zt';
        }
        if(in_array($type,$all_types['bk'])){
            return 'bk';
        }
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
            if(isset($res['long']) && count($res['noCacheArr'])>1){//dd($result);
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
    public static function custom($modelAlias, $method, $param){
        return ':'.self::confirm_type($modelAlias).'::'.$modelAlias.'::'.$method.'::'.(isset($param['classid'])?$param['classid']:'').self::filterStr($param);
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
            $result[$k]=Cache::get($v);
        }
        return $result;
    }

    /**
     * 详情页定制key
     *
     * @param $id
     * @return string
     */
    public static function DetailCache($id){
         return ':detail::specials'.$id;
    }
}
