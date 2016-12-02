<?php

namespace SuperView\Models;

use SuperView\Dal\Dal;
use SuperView\Utils\Page;

class BaseModel
{
    protected $dal;

    protected $virtualModel;

    protected $pageOptions;

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

    public function setPageOptions($pageOptions)
    {
        if (is_array($this->pageOptions)) {
            $this->pageOptions = array_merge($this->pageOptions, $pageOptions);
        } else {
            $this->pageOptions = $pageOptions;
        }
    }

    protected function getCurrentPage()
    {
        return isset($this->pageOptions['currentPage']) ? $this->pageOptions['currentPage'] : 0;
    }

    protected function returnWithPage($data, $limit)
    {
        if (empty($this->pageOptions)) {
            $response = $data['list'];
        } else {
            $page = new Page($this->pageOptions['route'], $data['count'], $limit, $this->pageOptions['currentPage'], $this->pageOptions['options']);
            $data['page'] = $page->render();
            $response = $data;
            $this->pageOptions = []; // 使用完销毁
        }

        return $response;
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
