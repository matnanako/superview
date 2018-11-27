<?php

namespace SuperView;

use Illuminate\Support\Facades\Cache;
use SuperView\Utils\Config as SConfig;
use SuperView\Utils\Cache as SCache;

/**
 * A view master to get data directly in the view template.
 */
class SuperView
{
    // 存储当前Model实例
    private $model;

     // 存放的是SuperView实例, 并不是Model实例
     // 其中SuperView实例里存储的$model是对应的Model实例
     // 每一个$modelAlias会生成一个Model实例, 每一个Model实例存储在一个SuperView实例中以被使用
     // 意义在于SuperView可以统一处理所有的Model方法调用
    private static $instances;

    private function __construct()
    {
        // 使用别名, 避免和框架全局类冲突
        class_exists('SConfig') ?: class_alias(SConfig::class, 'SConfig');
        class_exists('SCache') ?: class_alias(SCache::class, 'SCache');
    }

    private static function getInstance($modelAlias)
    {
        if (empty(self::$instances[$modelAlias])) {
            self::$instances[$modelAlias] = new self();
            self::$instances[$modelAlias]->model = self::getBindingModel($modelAlias);
        }

        return self::$instances[$modelAlias];
    }

    /**
     * Get binding model by model mapping
     *
     * @return object
     */
    private static function getBindingModel($modelAlias)
    {
        $models = SConfig::get('models');
        if (array_key_exists($modelAlias, $models)) {
            $model = $models[$modelAlias];
        } else {
            $model = $models['content'];
        }
        $model = $model::getInstance($modelAlias);

        return $model;
    }

    /**
     * @param  array  $configs
     * @return void
     */
    public static function setConfig($configs = [])
    {
        SConfig::import($configs);
    }

    /**
     * Set model.
     *
     * @param  string  $modelAlias
     * @return SuperView\SuperView
     */
    public static function get($modelAlias)
    {
        return self::getInstance($modelAlias);
    }

    /**
     * Set cache time.
     *
     * @param  string $minutes
     * @param  array  $keep 是否保持设置
     * @return SuperView\SuperView
     */
    public function cache($minutes, $keep = false)
    {
        \SCache::setCacheTime($minutes, $keep);

        return $this;
    }

    /**
     * Set page info.
     *
     * @param  string  $route url路由规则
     * @param  int  $currentPage 当前分页
     * @param  boolean  $simple 是否简洁模式
     * @param  array  $options 分页样式配置
     * @return SuperView\SuperView
     */
    public function page($route = null, $currentPage = 1, $simple = false, $options = [])
    {
        $this->model->setPageOptions(['route'=>$route, 'currentPage'=>$currentPage, 'simple'=>$simple, 'options'=>$options]);

        return $this;
    }

    /**
     * @param  string  $method
     * @param  array  $params
     * @return boolean | array
     */
    public function __call($method, $params)
    {
        $model = $this->model;

        if (empty($model) || !is_callable([$model, $method])) {
            return [];
        }

        //分类直接返回
        if($method=='children') {
            $data = $model->$method(...$params);
            return $data;
        }

        // 统一设置缓存
        $cacheMinutes = \SCache::getCacheTime();

        $arr=$this->makeCahce($params, $model, $method, $cacheMinutes);

       //先循环将没有缓存的数据组合，然后统一调取接口，对返回的数据拆分并进行缓存
        foreach($arr['detail'] as $key=>$cacheKey){
            // 如果cacheKey为false则该model不设置缓存(当存在false的情况证明必定是未二次修改的方法。不会以数组的形式执行，直接return便可)
            if ($cacheKey['cacheKey'] === false) {
                $data = $model->$method(...$params);
                return $data;
            } else {
                if(!\SCache::has($cacheKey['cacheKey'])){
                    if(isset($arr['long'])){
                        $new_arr[]=current($cacheKey[$arr['long']]);
                        $noCacheArr[current($cacheKey[$arr['long']])]=$cacheKey['cacheKey'];
                    }else{
                        $new_arr[]=$params;
                    }
                }
            }
        }
        //是数组且存在没有缓存的情况
        if(isset($arr['long']) && isset($new_arr)) {
            $params[$arr['long']] = $new_arr;
        }



        //统一请求api接口
        if(isset($new_arr)) {
            $result = $model->$method(...$params);
        }


        //new_arr可判断是否有值没有缓存
        if(isset($new_arr)){
            if(isset($arr['long']) && count($noCacheArr)>1){
               foreach($result as $k => $v){
                   Cache::put($noCacheArr[$k], $v['list'], $cacheMinutes);
               }
            }elseif(isset($arr['long']) && count($noCacheArr)==1){
                //防止数组中仅存在一个没有缓存的时候，此时请求接口传递的为单个数组如[1] ，此时接口返回的结果不包含cid为下标的返回
                   Cache::put(current($noCacheArr), $result['list'], $cacheMinutes);
            }else{
                //针对未修改的方法（如superTopic）直接返回的list的值 故加判断。
                if(isset($result['list'])) {
                    Cache::put(current($arr['detail'])['cacheKey'], $result['list'], $cacheMinutes);
                }else{
                    Cache::put(current($arr['detail'])['cacheKey'], $result, $cacheMinutes);
                }
            }
        }


        //读取缓存数据 todo 根据实际需要取缓存值
        foreach($arr['detail'] as $k => $v){
            $data[]=Cache::get($v['cacheKey']);
        }

        // 重置$model状态(目前包括去除分页设置)
        // reset方法不可以在$model内自动调用,
        // 因为如果缓存命中, $model的$method方法不会被执行
        $model->reset();

        return $data;
    }
    //先拆分数组并生成缓存key
    public function makeCahce($params, $model, $method, $cacheMinutes){
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
      return $result;
    }
}
