<?php

namespace SuperView\Dal\Api;

/**
* Tag Dal.
*/
class Tag extends Base
{
    
    public function getList($classid, $isGood, $page, $limit, $order)
    {
        $params = [
            'cid'    => intval($classid),
            'isgood' => intval($isGood),
            'page'   => intval($page),
            'limit'  => intval($limit),
            'order'  => $order,
        ];
        return $this->getData('index', $params);
    }

    public function getInfo($tagname)
    {
        $params = [
            'tagname'    => $tagname
        ];
        return $this->getData('info', $params);
    }
}