<?php

namespace SuperView;

use SuperView\Utils\Config as SConfig;
use SuperView\Utils\Cache as SCache;

/**
 * A view master to get data directly in the view template.
 */
class SuperView
{
    private static $model;

    private static $instance;

    private function __construct()
    {
        class_alias(SConfig::class, 'SConfig');
        class_alias(SCache::class, 'SCache');
    }

    private static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }

        return self::$instance;
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
        self::$model = self::getBindingModel($modelAlias);

        return self::getInstance();
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
            $model = $model::getInstance();
        } else {
            $model = $models['content'];
            $model = $model::getInstance();
            $model->setVirtualModel($modelAlias);
        }

        return $model;
    }

    /**
     * Set cache time.
     * 
     * @param  string  $minutes
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
     * @param  string  $minutes
     * @param  array  $keep 是否保持设置
     * @return SuperView\SuperView
     */
    public function page($route, $page = 1, $options = [])
    {
        self::$model->setPageOptions(['route'=>$route, 'currentPage'=>$page, 'options'=>$options]);

        return $this;
    }

    /**
     * @param  string  $method
     * @param  array  $params
     * @return array
     */
    public function __call($method, $params)
    {
        $model = self::$model;
        if (empty($model) || !is_callable([$model, $method])) {
            return false;
        }

        // 统一设置缓存，如果cache_key为false则该model不设置缓存.
        $cache_minutes = \SCache::getCacheTime();
        $cache_key     = \SCache::getCacheKey($model, $method, $params, $cache_minutes);
        if ($cache_key === false) {
            $data = $model->$method(...$params);
        } else {
            $data = \SCache::remember($cache_key, $cache_minutes, function() use ($model, $method, $params) {
                $data = $model->$method(...$params);
                return $data;
            });
        }

        return $data;
    }

}
