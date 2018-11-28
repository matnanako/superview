<?php

namespace SuperView\Utils;

/**
 * 配置类, 所有方法静态调用, 无需初始化配置.
 */
class CacheKey
{
    public static function makeCachekey($method, $params , $model ,$virtualModel){
        $key=self::confirm_type($virtualModel);
        $key.='::' .$virtualModel;
        $key.='::' . $method;
        //反射获取方法默认参数以及默认值
        $param=self::reflex($model,$method);
         foreach ($param as $k=>$value) {
            $depend[$k]['name'] = $value->getName();
            if ($value->isOptional()) {
                $depend[$k]['default'] = $value->getDefaultValue();
            }
        }
        //设置缓存值(从前往后，将实际参数插入默认参数中，然后classid排在第一个，然后去除limit，ispic，其余参数按顺序排列)
        foreach($params as $ke=>$ve){
            if(is_array($ve)){
                $change_default=current($ve);
            }else{
                $change_default=$ve;
            }
            $depend[$ke]['default']=$change_default;
            if($depend[$ke]['name']=='classid'){
                $key.='::' .$change_default;
            }
        }
        foreach($depend as $k=>$v){
            if(!in_array($v['name'],['limit','isPic','classid'])){
                $key.='::' .$v['default'];
            }
        }
        return $key;
    }
    //确认支点
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
            return 'artcicle';
        }
        if(in_array($type,$all_types['zt'])){
            return 'zt';
        }
    }
    //反射获取方法参数
    public static  function reflex($model, $method){
        $ReflectionFunc = new \ReflectionMethod($model, $method);
        return $ReflectionFunc->getParameters();
    }

    //根据实际传入参数先拆分数组并生成缓存key插入数组
    public static function insertCahce($params, $model, $method, $cacheMinutes){
        foreach($params as $k=>$v){
            if(count($v)>1){
                $result['long']=$k;
                foreach($v as $ke=>$ve){
                    $result['detail'][$ke]=$params;
                    $result['detail'][$ke][$k]=[$ve];
                    $result['detail'][$ke]['cacheKey'] = \SCache::getCacheKey($model, $method, $result['detail'][$ke], $cacheMinutes);
                }
            }
        }
        if(!isset($result['long'])){
            $result['detail'][0]=$params;
            $result['detail'][0]['cacheKey'] = \SCache::getCacheKey($model, $method, $params, $cacheMinutes);
        }
        return self::assemble($result, $params);
    }
    public static function assemble($result, $params){
        //将没有缓存的数据组合！new_arr区分是否有未缓存的数据。params为默认传递过来去除已有缓存和不需要缓存的的新值，用于统一传递api请求
        foreach($result['detail'] as $key=>$cacheKey){
            if(!\SCache::has($cacheKey['cacheKey'])){
                if(isset($result['long'])){
                    $new_arr[]=current($cacheKey[$result['long']]);
                    $result['noCacheArr'][current($cacheKey[$result['long']])]=$cacheKey['cacheKey'];
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
    //生成缓存
    public static function makeCache($result, $res ,$cacheMinutes){
        if(isset($res['new_arr'])){
            if(isset($res['long']) && count($res['noCacheArr'])>1){
                foreach($result as $k => $v){
                    Cache::put($res['noCacheArr'][$k], $v['list'], $cacheMinutes);
                }
            }elseif(isset($res['long']) && count($res['noCacheArr'])==1){
                //防止数组中仅存在一个没有缓存的时候，此时请求接口传递的为单个数组如[1] ，此时接口返回的结果不包含cid为下标的返回
                Cache::put(current($res['noCacheArr']), $result['list'], $cacheMinutes);
            }else{
                //针对未修改的方法（如superTopic）直接返回的list的值 故加判断。
                if(isset($result['list'])) {//dd($result['list']);
                    Cache::put(current($res['detail'])['cacheKey'], $result['list'], $cacheMinutes);
                }else{
                    Cache::put(current($res['detail'])['cacheKey'], $result, $cacheMinutes);
                }
            }
        }
    }
    //读取缓存
    public static function getCache($arr){
        if(count($arr['detail'])==1){
           return Cache::get($arr['detail'][0]['cacheKey']);
        }
        foreach($arr['detail'] as $k=> $v){
            $classid=is_array($arr['detail'][$k][$arr['long']])?$arr['detail'][$k][$arr['long']][0]:$arr['detail'][$k][$arr['long']];
            $data[$classid]=Cache::get($v['cacheKey']);
        }
        return $data;
    }
}
