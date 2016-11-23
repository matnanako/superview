<?php

namespace SuperView\Models;

class TagModel extends BaseModel
{

    /**
     * TAG列表  
     */
    public function getInfo($params = [])
    {
        $data = $this->checkParams($params);
        $data = $this->dal['tag']->getInfo($params);
        return $data;
    }

    /**
     * 友情链接
     */
    public function getUrl($params = [])
    {
        $data = $this->checkParams($params);
        $data = $this->dal['tag']->getfriendUrl($params);
        return $data;
    }

}