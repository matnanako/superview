<?php

namespace SuperView\Dal\Api;

/**
* Tag Dal.
*/
class Tag extends Base
{
    
    public function getInfo($params)
    {
        $params['a'] = 'lists';
        $params['c'] = $this->getClassInfo(__CLASS__);
        $data = $this->makeData($params);
        return $data;
    }

    public function getfriendUrl($params)
    {
        $params['a'] = 'friendlinks';
        $params['c'] = $this->getClassInfo(__CLASS__);
        $data = $this->makeData($params);
        return $data;
    }
}