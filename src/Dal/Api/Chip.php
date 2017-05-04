<?php

namespace SuperView\Dal\Api;

/**
* Chip Dal. （碎片）
*/
class Chip extends Base
{
    
    public function getList($chipid, $classid, $page, $limit)
    {
        $params = [
            'cid'    => intval($classid),
            'classid' => intval($isGood),
            'page'   => intval($page),
            'limit'  => intval($limit)
        ];
        return $this->getData('infolist', $params);
    }

    public function getInfo($chipid)
    {
        $params = [
            'cid'    => $chipid
        ];
        return $this->getData('detail', $params);
    }
}