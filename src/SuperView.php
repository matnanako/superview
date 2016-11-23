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
        $params = isset($params[0]) ? $params[0] : []; // get param, e.g.['limit'=>1].

        // Get model to query data.
        $model = $this->getCallBindingModel();

        if (empty($model)) {
            return [];
        }

        $cache_key = $model->makeCacheKey($method, $params);
        if (empty($cache_key)) {
            throw new \SuperView\Exceptions\BaseException("No cache key!");
        }

        // Get data from cache.
        $cache_minutes = config('app.debug') ? 0 : \Config::get('cache_minutes');
        $data = \Cache::remember($cache_key, $cache_minutes, function() use ($model, $method, $params) {

            // Get the response from super model.
            $response = !is_callable([$model, $method]) ? [] : $model->$method($params);

            return $response;
        });

        return $data;
    }

    /**
     * Get binding model by model mapping
     * which is configured in config.php
     * 
     * @return object
     */
    private function getCallBindingModel()
    {
        if (array_key_exists($this->model, \Config::get('models'))) {
            $model = app(\Config::get('model_prefix') . $this->model);
        } else {
            $model = app(\Config::get('model_prefix') . 'content');
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
