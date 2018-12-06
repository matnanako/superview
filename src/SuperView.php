<?php

namespace SuperView;

use Illuminate\Support\Facades\Cache;
use SuperView\Models\CategoryModel;
use SuperView\Utils\CacheKey;
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
     * Set filter info.
     *
     */
    public function filter($filter = 'info')
    {
        $this->model->setFilterOptions($filter);

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

        //分类相关与分页直接返回  (专题中的分类不能通过model确定，需要通过方法cagtegories)
        if(($model instanceof CategoryModel) ||  $this->model->isPage() || $method=='categories') {
            $data = $model->$method(...$params);
            $model->reset();
            return $data;
        }

        // 统一设置缓存
        $cacheMinutes = \SCache::getCacheTime();
        $res=CacheKey::insertCahce($params, $model, $method, $cacheMinutes);


        //统一请求api接口
        if(isset($res['new_arr'])) {
            $apiResult = $model->$method(...$res['params']);

            //插入缓存
            CacheKey::makeCache($apiResult, $res ,$cacheMinutes);
        }

         //统一读取缓存数据
        $data=CacheKey::getCache($res);


        // 重置$model状态(目前包括去除分页设置)
        // reset方法不可以在$model内自动调用,
        // 因为如果缓存命中, $model的$method方法不会被执行
        $model->reset();

        return $data;
    }

}
