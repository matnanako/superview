<?php

namespace SuperView\Models;

class TagModel extends BaseModel
{

    /**
     * TAG列表
     */
    public function index($classid = 0, $isGood = 0, $page = 1, $limit = 20, $order = 'addtime')
    {
        $data = $this->dal['tag']->getList($classid, $isGood, $page, $limit, $order);
        return $data;
    }

}