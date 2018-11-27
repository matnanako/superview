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
        $this->virtualDal = 'zt';
    }

    /**
     * 专题列表
     * @return boolean | array
     */
    public function getList($topicCategoryId, $classid, $page, $limit, $order)
    {
        $params = [
            'zcid'  => ($topicCategoryId),
            'cid'   => ($classid),
            'page'  => intval($page),
            'limit' => intval($limit),
            'order' => $order,
        ];
        return $this->getData('lists', $params);
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
        $categories = $this->getData('classlist')['list'];
        foreach ($categories as $category) {
            $categoryIndex[$category['classid']] = $category;
        }
        return $categoryIndex;
    }


    /**
     * 专题信息列表
     * @return boolean | array
     */
    public function getContentByTopicId($topicId, $page, $limit)
    {
        $params = [
            'ztid'  => intval($topicId),
            'page'  => intval($page),
            'limit' => intval($limit),
        ];
        return $this->getData('infolist', $params);
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
}
