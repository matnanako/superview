<?php

namespace SuperView\Dal\Api;

/**
* Tag Dal.
*/
class Utils extends Base
{
    
    public function getFriendLinks($type, $classid, $limit)
    {
        $params = [
            'type'    => intval($type),
            'classid' => intval($classid),
            'num'     => intval($limit),
        ];
        return $this->getData('friendlinks', $params);
    }


}