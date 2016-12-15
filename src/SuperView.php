<?php

namespace SuperView;

use SuperView\Utils\Config as SConfig;
use SuperView\Utils\Cache as SCache;

/**
 * A view master to get data directly in the view template.
 */
class SuperView
{
    private $model;

    private static $instances;

    private function __construct()
    {
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
            $model = $model::getInstance($modelAlias);
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
    public function page($route = null, $page = 1, $options = [])
    {
        $this->model->setPageOptions(['route'=>$route, 'currentPage'=>$page, 'options'=>$options]);

        return $this;
    }

    /**
     * @param  string  $method
     * @param  array  $params
     * @return array
     */
    public function __call($method, $params)
    {
        $model = $this->model;
        if (empty($model) || !is_callable([$model, $method])) {
            return false;
        }

        // 统一设置缓存，如果cacheKey为false则该model不设置缓存.
        $cacheMinutes = \SCache::getCacheTime();
        $cacheKey     = \SCache::getCacheKey($model, $method, $params, $cacheMinutes);
        if ($cacheKey === false) {
            $data = $model->$method(...$params);
        } else {
            $data = \SCache::remember($cacheKey, $cacheMinutes, function () use ($model, $method, $params) {
                $data = $model->$method(...$params);
                return $data;
            });
            // 如果数据为空, 不保存缓存
            if (empty($data)) {
                \SCache::forget($cacheKey);
            }
        }

        return $data;
    }
}
