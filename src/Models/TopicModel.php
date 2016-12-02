<?php

namespace SuperView\Models;

class TopicModel extends BaseModel
{

    /**
     * 专题列表
     */
    public function index($topicCategoryId = 0, $classid = 0, $page = 1, $limit = 0, $order = 'addtime')
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
        if (empty($id)) {
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
        $data = $this->dal['topic']->getCategories();
        return $data;
    }
}
