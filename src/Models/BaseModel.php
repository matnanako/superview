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

    public static function getInstance($virtualModel = '')
    {
        // 只有content model需要$virtualModel, 其它model为空.
        // 使用$virtualModel或当前类名作为实例的键名,
        // 保证不同$virtualModel的content model使用不同的实例
        $key = empty($virtualModel) ? static::class : $virtualModel;
        if (empty(static::$instances[$key])) {
            static::$instances[$key] = new static();
            static::$instances[$key]->setVirtualModel($virtualModel);
        }

        return static::$instances[$key];
    }

    protected function setVirtualModel($virtualModel)
    {
        $this->virtualModel = $virtualModel;
    }

    /**
     * 设置分页属性.
     *
     * @return void
     */
    public function setPageOptions($pageOptions)
    {
        if (is_array($this->pageOptions)) {
            $this->pageOptions = array_merge($this->pageOptions, $pageOptions);
        } else {
            $this->pageOptions = $pageOptions;
        }
    }

    /**
     * 重置当前Model的属性(目前包含分页属性).
     *
     * @return void
     */
    public function reset()
    {
        $this->pageOptions = null;
    }

    /**
     * 使用支持分页的方式返回数组, 包含'list', 'count', 'page'参数.
     *
     * @return array
     */
    protected function returnWithPage($data, $limit)
    {
        $data['list'] = empty($data['list']) ? [] : $data['list'];
        $data['count'] = empty($data['count']) ? 0 : $data['count'];
        // 未设置分页url路由规则, 直接返回'list'包含数组.
        if (empty($this->pageOptions) || $this->pageOptions['route'] === false) {
            $response = $data['list'];
        } else {
            $data['page'] = "";
            if (!empty($this->pageOptions['route'])) {
                $page = new Page($this->pageOptions['route'], $data['count'], $limit, $this->pageOptions['currentPage'], $this->pageOptions['simple'], $this->pageOptions['options']);
                $data['page'] = $page->render();
            }
            $response = $data;
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
        $page = $this->getCurrentPage();
        return md5(\SConfig::get('api_base_url') . get_class($this) . ':' . $this->virtualModel . ':page:' . $page . $method . http_build_query($params));
    }

    /**
     * 获取通过SuperView::page方法设置的page参数.
     *
     * @return string
     */
    protected function getCurrentPage()
    {
        return isset($this->pageOptions['currentPage']) ? $this->pageOptions['currentPage'] : 1;
    }
}
