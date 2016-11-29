<?php

namespace SuperView\Models;

class UtilsModel extends BaseModel
{

    /**
     * 专题列表
     */
    public function friendLinks($type = 0, $classid = 0, $limit = 20)
    {
        $data = $this->dal['utils']->getFriendLinks($type, $classid, $limit);
        return $data;
    }

}
