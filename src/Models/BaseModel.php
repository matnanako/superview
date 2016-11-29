<?php

namespace SuperView\Models;

use SuperView\Dal\Dal;

class BaseModel
{
    protected $dal;

    protected $virtualModel;

    protected static $instance;

    private function __construct()
    {
        $this->dal = Dal::getInstance();
    }

    public static function getInstance()
    {
        if (!(static::$instance instanceof static)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function setVirtualModel($virtualModel)
    {
        $this->virtualModel = $virtualModel;
    }

    /**
     * Generate cache key by params.
     * 
     * @return string
     */
    public function makeCacheKey($method, $params = [])
    {
        return md5(\SConfig::get('api_base_url') . get_class($this) . ':' . $this->virtualModel . ':' . $method . http_build_query($params));
    }
}
