<?php

namespace SuperView\Models;

class TagModel extends BaseModel
{

    /**
     * TAG列表
     */
    public function index($isGood = 0, $classid = 0, $limit = 0, $order = 'addtime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal['tag']->getList($classid, $isGood, $page, $limit, $order);
        return $this->returnWithPage($data, $limit);
    }

    public function indexCount($isGood = 0, $classid = 0, $limit = 0, $order = 'addtime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal['tag']->getList($classid, $isGood, $page, $limit, $order);
        if(empty($data['count'])){
            return -1;
        }
        return $data['count'];
    }

    /**
     * 根据tagname查询tag信息
     */
    public function info($tagname)
    {
        if (empty($tagname)) {
            return [];
        }
        $data = $this->dal['tag']->getInfo($tagname);
        return $data;
    }
}