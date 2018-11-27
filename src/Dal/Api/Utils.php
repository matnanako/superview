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
            'type'    => ($type),
            'classid' => ($classid),
            'num'     => intval($limit),
        ];
        return $this->getData('friendlinks', $params);
    }


}