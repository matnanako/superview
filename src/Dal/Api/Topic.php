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
            'zcid'  => intval($topicCategoryId),
            'cid'   => intval($classid),
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
        return $this->getData('classlist');
    }
    
}