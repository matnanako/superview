<?php

namespace SuperView\Models;

use SuperView\Dal\Dal;
use SuperView\Utils\Page;

class BaseModel
{
    protected $dal;

    protected $virtualModel;

    protected $pageOptions;

    protected static $instances;

    private function __construct()
    {
        $this->dal = Dal::getInstance();
    }

    public static function getInstance($virtualModel = 'default')
    {
        // 只有content model需要'$virtualModel', 其它model使用默认值
        if (empty(static::$instances[$virtualModel]) || !(static::$instances[$virtualModel] instanceof static)) {
            static::$instances[$virtualModel] = new static();
            static::$instances[$virtualModel]->setVirtualModel($virtualModel);
        }

        return static::$instances[$virtualModel];
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
        return isset($this->pageOptions['currentPage']) ? $this->pageOptions['currentPage'] : 1;
    }

    protected function returnWithPage($data, $limit)
    {
        $data['list'] = empty($data['list']) ? [] : $data['list'];
        $data['count'] = empty($data['count']) ? [] : $data['count'];
        if (empty($this->pageOptions) || $this->pageOptions['route'] === false) {
            $response = $data['list'];
        } else {
            if ($this->pageOptions['route'] !== null) {
                $page = new Page($this->pageOptions['route'], $data['count'], $limit, $this->pageOptions['currentPage'], $this->pageOptions['options']);
                $data['page'] = $page->render();
            }
            $response = $data;
            $this->pageOptions = null; // 使用完销毁
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
