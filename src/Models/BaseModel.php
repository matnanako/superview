<?php

namespace SuperView\Models;

use SuperView\Dal\Dal;
use SuperView\Utils\CacheKey;
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
        // 每一个$virtualModel对应一个独立的实例
        if (empty(static::$instances[$virtualModel])) {
            static::$instances[$virtualModel] = new static();
            static::$instances[$virtualModel]->setVirtualModel($virtualModel);
        }

        return static::$instances[$virtualModel];
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
//  //缓存名称方式重构
    public function makeCacheKey($method, $params = [], $model='')
    {
        //树缓存单独拧出
        if($method=='SuperView\Models\CategoryModel::all'){
            return md5(\SConfig::get('api_base_url') . get_class($this). ':' . $method  . ':' . $this->virtualModel . ':' . http_build_query($this->pageOptions?:[]) . ':' . http_build_query($params));
        }
       return CacheKey::makeCachekey($method, $params , $model ,$this->virtualModel);
    }

    /**
     * 获取通过SuperView::page方法设置的page参数.
     *
     * @return string
     */
    public function getCurrentPage()
    {
        return isset($this->pageOptions['currentPage']) ? $this->pageOptions['currentPage'] : 1;
    }

    /**
     * 添加列表包含信息：分类信息、url.
     *
     * @return void
     */
    public function addListInfo(&$data)
    {
        if (!isset($data['list'])) {
            $data = [];
            return;
        }
        $categoryModel = CategoryModel::getInstance('category');

        if (isset($data['status']) && $data['status']==0) {
            //单个查询
            foreach ($data['list']['list'] as $key => &$value) {
                $category = $categoryModel->info($value['classid']);
                $value['infourl'] = $this->infoUrl($value['id'], $category);
                $value['classname'] = $category['classname'];
                $value['classurl'] = $categoryModel->categoryUrl($value['classid']);
                $value['category'] = $category;
            }
        }elseif(isset($data['status']) && $data['status']==1 ) {
            //多个查询
            foreach ($data['list'] as $ke => &$ve) {
                foreach ($ve['list'] as $k => &$v) {
                    $category = $categoryModel->info($v['classid']);
                    $v['infourl'] = $this->infoUrl($v['id'], $category);
                    $v['classname'] = $category['classname'];
                    $v['classurl'] = $categoryModel->categoryUrl($v['classid']);
                    $v['category'] = $category;
                }
            }
        }else{
            //非数组形式查询
                foreach ($data['list'] as $key => &$value) {
                    $category = $categoryModel->info($value['classid']);
                    $value['infourl'] = $this->infoUrl($value['id'], $category);
                    $value['classname'] = $category['classname'];
                    $value['classurl'] = $categoryModel->categoryUrl($value['classid']);
                    $value['category'] = $category;
                }
            }
     }

    /**
     * 获取详情页url.
     */
    private function infoUrl($id, $category)
    {
        $infoUrlTpl = \SConfig::get('info_url');
        $infourl = str_replace(
            ['{channel}', '{classname}', '{classid}', '{id}'],
            [$category['channel'], $category['bname'], $category['classid'], $id],
            $infoUrlTpl
        );
        return $infourl;
    }
}
