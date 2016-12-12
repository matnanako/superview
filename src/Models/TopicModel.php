<?php

namespace SuperView\Models;

class TopicModel extends BaseModel
{

    /**
     * 专题列表
     */
    public function index($topicCategoryId = 0, $classid = 0, $limit = 0, $order = 'addtime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal['topic']->getList($topicCategoryId, $classid, $page, $limit, $order);
        return $this->returnWithPage($data, $limit);
        return $data;
    }

    /**
     * 专题详情
     */
    public function info($id, $path = '')
    {
        if (empty($id) && empty($path)) {
            return false;
        }
        $data = $this->dal['topic']->getInfo($id, $path);
        return $data;
    }

    /**
     * 专题分类列表
     */
    public function categories()
    {
        $categories = $this->dal['topic']->getCategories();
        return $categories;
    }

}
