<?php

namespace SuperView\Models;

class TagModel extends BaseModel
{

    /**
     * TAG列表
     */
    public function index($isGood = 0, $classid = 0, $limit = 20, $order = 'addtime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal['tag']->getList($classid, $isGood, $page, $limit, $order);
        dump($data);
        return $this->returnWithPage($data, $limit);
    }

}