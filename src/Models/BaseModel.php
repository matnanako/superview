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
    { dd($data);
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
        //dd($response);
        return $response;
    }

    /**
     * Generate cache key by params.
     *
     * @return string
     */
//    public function makeCacheKey($method, $params = [])
//    {
//        return md5(\SConfig::get('api_base_url') . get_class($this). ':' . $method  . ':' . $this->virtualModel . ':' . http_build_query($this->pageOptions?:[]) . ':' . http_build_query($params));
//    }
//  //缓存名称方式重构
    public function makeCacheKey($method, $params = [])
    {
        //树缓存单独拧出
        if($method=='SuperView\Models\CategoryModel::all'){
            return md5(\SConfig::get('api_base_url') . get_class($this). ':' . $method  . ':' . $this->virtualModel . ':' . http_build_query($this->pageOptions?:[]) . ':' . http_build_query($params));
        }

    //通过对应参数确定缓存名称
        $key=$this->confirm_type();
        $key.=':' . $this->virtualModel;
        $key.=':' . $method;
        $result = $this->method_split($key, $method, $params);
        return $result;
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
        //dd($data);
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
    public function confirm_type(){
        $all_types = \SConfig::get('type');
        $type=$this->virtualModel;
        if(in_array($type,$all_types['soft'])){
            return 'soft';
        }
        if(in_array($type,$all_types['category'])){
            return 'category';
        }
        if(in_array($type,$all_types['article'])){
            return 'artcicle';
        }
        if(in_array($type,$all_types['zt'])){
            return 'zt';
        }
    }
    public function  method_split($key, $method, $parmes){
            $str=false;
            switch($method){
                 //($level = [0], $classid = [0], $limit = 0, $isPic = 0, $order = 'newstime')
                case 'top':   //top方法传递的时候参数1和2 只能以数组的形势
                     $str = current($parmes[1]);
                     $str.= ':' .current($parmes[0]);
                     isset($parmes[4])? $str.=':' .$parmes[4] : $str .= ':newstime';
                    break;
                //$classid = [0], $limit = 0, $isPic = 0
                case 'recent':
                    $str = current($parmes[0]);
                    break;
                //$period = 'all', $classid = 0
                case 'count':
                    $str = isset($parmes[1]) ? $parmes[1] : 0;
                    isset($parmes[0]) ? $str .=':'. $parmes[0] : $str .= ':all';
                    break;
                    //$topicCategoryId = [0], $classid = [0], $limit = 0, $order = 'addtime'
                case 'index':
                    $str = isset($parmes[0]) ? current($parmes[0]) : 0;
                    isset($parmes[1]) ? $str .=':'. current($parmes[1]) : $str .= ':0';
                    isset($parmes[3]) ? $str .=':'. $parmes[3] : $str .= ':addtime';
                    break;
                   // $topicId = 0, $limit = 0
                case 'superTopic':
                    $str = isset($parmes[0]) ? ($parmes[0]) : 0;
                    break;
                    //$period = ['all'], $classid = [0], $limit = 0, $isPic = 0
                case 'rank':
                    $str = isset($parmes[1]) ? current($parmes[1]) : 0;
                    isset($parmes[0]) ? $str .=':'. current($parmes[0]) : $str .= ':all';
                     break;
                //$level = 0, $classid = 0, $limit = 0, $isPic = 0, $order = 'newstime'
                case 'good':
                    $str = isset($parmes[1]) ? current($parmes[1]) : 0;
                    isset($parmes[0]) ? $str .=':'. current($parmes[0]) : $str .= ':0';
                    isset($parmes[4]) ? $str .=':'. $parmes[4] : $str .= ':newstime';
                    break;
                //$type = 0, $classid = [0], $limit = 0
                case 'friendLinks':
                    $str = isset($parmes[1]) ? current($parmes[1]) : 0;
                    break;
                //$level = 0, $classid = [0], $limit = 0, $isPic = 0, $order = 'newstime'
                case 'firsttitle':
                    $str = isset($parmes[1]) ? current($parmes[1]) : 0;
                    isset($parmes[4]) ? $str .=':'. $parmes[4] : $str .= ':newstime';
                    break;
            }
       if($str!==false){
           $str=$key.':'.$str;
       }
        return $str;
    }
}
