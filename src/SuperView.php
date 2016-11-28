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
     * @param  string  $model
     * @return object
     */
    public static function get($model)
    {
        self::$model = $model;

        return self::getInstance();
    }

    /**
     * Set cache time.
     * 
     * @param  string  $minutes
     * @param  array  $keep 是否保持设置
     * @return object
     */
    public function cache($minutes, $keep = false)
    {
        \SCache::setCacheTime($minutes, $keep);

        return $this;
    }

    /**
     * @param  string  $method
     * @param  array  $params
     * @return array
     */
    public function __call($method, $params)
    {
        // Get model to query data.
        $model = self::getCallBindingModel();

        if (empty($model) || !is_callable([$model, $method])) {
            return [];
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


    /**
     * Get binding model by model mapping
     * 
     * @return object
     */
    private function getCallBindingModel()
    {
        $models = \SConfig::get('models');
        if (array_key_exists(self::$model, $models)) {
            $model = $models[self::$model];
            $model = $model::getInstance();
        } else {
            $model = $models['content'];
            $model = $model::getInstance();
            $model->setVirtualModel(self::$model);
        }

        return $model;
    }

}
