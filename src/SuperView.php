<?php

namespace SuperView;

use ArrayAccess;
use SuperView\Utils\Config;

/**
 * A view master to get data directly in the view template.
 */
class SuperView implements ArrayAccess
{
    private $model;

    private $cacheMinutes;

    private $keepCacheTime;

    private static $instance;

    private function __construct($configs)
    {
        class_alias(Config::class, 'Config');
        Config::import($configs);
    }

    /**
     * @param  array  $configs
     * @return object
     */
    public static function getInstance($configs = [])
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self($configs);
        }

        return self::$instance;
    }

    /**
     * @param  string  $method
     * @param  array  $params
     * @return array
     */
    public function __call($method, $params)
    {
        // Get model to query data.
        $model = $this->getCallBindingModel();

        if (empty($model) || !is_callable([$model, $method])) {
            return [];
        }

        // 统一设置缓存，如果cache_key为false则该model不设置缓存.
        $cache_minutes = $this->getCacheTime();
        $cache_key     = $this->getCacheKey($model, $method, $params, $cache_minutes);
        if ($cache_key === false) {
            $data = $model->$method(...$params);
        } else {
            $data = \Cache::remember($cache_key, $cache_minutes, function() use ($model, $method, $params) {
                $data = $model->$method(...$params);
                return $data;
            });
        }

        return $data;
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
        $this->cacheMinutes = $minutes;
        $this->keepCacheTime = $keep;

        return $this;
    }

    /**
     * Get cache time.
     * 
     * @return object
     */
    private function getCacheTime()
    {
        $cache_minutes = empty($this->cacheMinutes) ? \Config::get('cache_minutes') : $this->cacheMinutes;

        // 如果不需要保持设置，使用之后清空$this->cacheMinutes。
        if (!$this->keepCacheTime) {
            $this->cacheMinutes = 0;
        }

        return $cache_minutes;
    }

    /**
     * Get cache key.
     * 
     * @return object
     */
    private function getCachekey($model, $method, $params, $cache_minutes)
    {
        $params['cache'] = $cache_minutes;
        $cache_key = $model->makeCacheKey($method, $params);

        return $cache_key;
    }

    /**
     * Get binding model by model mapping
     * 
     * @return object
     */
    private function getCallBindingModel()
    {
        $models = \Config::get('models');
        if (array_key_exists($this->model, $models)) {
            $model = $models[$this->model];
            $model = $model::getInstance();
        } else {
            $model = $models['content'];
            $model = $model::getInstance();
            $model->setVirtualModel($this->model);
        }

        return $model;
    }

    /**
     * Set model by using "$superview['model']"
     * 
     * @return object
     */
    public function offsetGet($offset)
    {
        $this->model = $offset;
        return $this;
    }

    public function offsetExists($offset) {}

    public function offsetSet($offset, $value) {}

    public function offsetUnset($offset) {}

}
