<?php

namespace SuperView\Dal\Api;

/**
* Topic Dal.
*/
class Topic extends Base
{

    // 覆盖virtualDal.
    public function __construct($virtualDal)
    {
        parent::__construct($virtualDal);
        //$this->virtualDal = 'zt';
    }

    /**
     * 专题列表
     * @return boolean | array
     */
    public function getList($zcid, $classid, $page, $limit, $order)
    {
        $params = [
            'zcid'  => ($zcid),
            'cid'   => ($classid),
            'page'  => intval($page),
            'limit' => intval($limit),
            'order' => $order,
        ];
        return $this->getData('index', $params);
    }

    /**
     * 专题详情
     * @return boolean | array
     */
    public function getInfo($id, $path)
    {
        $params = [
            'id'   => intval($id),
            'path' => $path,
        ];
        return $this->getData('info', $params);
    }

    /**
     * 专题分类列表
     * @return boolean | array
     */
    public function getCategories()
    {
        $categories = $this->getData('categories')['list'];
        foreach ($categories as $category) {
            $categoryIndex[$category['classid']] = $category;
        }
        return $categoryIndex;
    }


    /**
     * 专题信息列表
     * @return boolean | array
     */
    public function getContentByTopicId($ztid, $page, $limit)
    {
        $params = [
            'ztid'  => intval($ztid),
            'page'  => intval($page),
            'limit' => intval($limit),
        ];
        return $this->getData('superTopic', $params);
    }

    /**
     * 与专题相同tag的信息列表
     *
     */
    public function taginfo($ztid, $classid, $page, $limit)
    {
        $params = [
            'ztid'  => intval($ztid),
            'classid'  => intval($classid),
            'page'  => intval($page),
            'limit' => intval($limit),
        ];
        return $this->getData('taginfo', $params);
    }
    /**
     * 详情页定制接口
     *
     * @param $id
     * @param $baikelimit
     * @param $softlimit
     * @return array|bool
     */
    public function getSpecials($id, $model, $baikelimit, $softlimit)
    {
        $params = [
            'id'    => $id,
            'model' => $model,
            'baikelimit' => $baikelimit,
            'softlimit' => $softlimit,
        ];
        return $this->getData('specials', $params);
    }

    /**
     * 天极定制接口
     *
     * @param $id
     * @param $limit
     * @param $baikelimit
     * @return array|bool|mixed
     */
    public function getTopics($id, $limit, $baikelimit)
    {
        $params = [
            'id'  => intval($id),
            'baikelimit' => $baikelimit,
            'limit' => intval($limit),
        ];
        return $this->getData('topics', $params);

    }

    /**
     * 专题自定义查询 专题自定义查询
     *
     * @param $field
     * @param $value
     * @param $classid
     * @param $limit
     * @param $order
     * @param $page
     * @return array|bool|mixed
     */
    public function getMatch($field, $value, $classid, $limit, $order, $page)
    {
        $params = [
            'field'  => $field,
            'value' => $value,
            'classid' => intval($classid),
            'limit' => intval($limit),
            'order' => $order,
            'page' => $page,
        ];
        return $this->getData('match', $params);
    }

    /**
     * 获取标签
     *
     * @param $ztid
     * @param $id
     * @param $classid
     * @param $limit
     * @return array|bool|mixed
     */
    public function getLabel($ztid, $id, $classid, $limit)
    {
        $params = [
            'ztid' => intval($ztid),
            'id' => intval($id),
            'classid' => intval($classid),
            'limit' => intval($limit)
        ];
        return $this->getData('label', $params);
    }

    /**
     * 热门标签
     *
     * @param $ztid
     * @param $limit
     * @return mixed
     */
    public function getHotLabel($ztid, $limit)
    {
        $params = [
            'ztid' => intval($ztid),
            'limit'=> intval($limit)
        ];
        return $this->getData('hotLabel', $params);
    }

    /**
     * 专题标签下关联软件列表
     *
     * @param $ztid
     * @param $labelid
     * @param $limit
     * @return array|bool|mixed
     */
    public function getLabelList($ztid, $labelid, $limit, $page)
    {
        $params = [
            'ztid' => intval($ztid),
            'labelid' => intval($labelid),
            'limit' => intval($limit),
            'page' => $page
        ];
        return $this->getData('labelList', $params);
    }

    /**
     * 获取所有标签
     *
     * @return array|bool|mixed
     */
    public function getAllLabel()
    {
        return $this->getData('allLabel');
    }
}
