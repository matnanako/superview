<?php

namespace SuperView\Dal\Api;

/**
* Topic Dal.
*/
class Topic extends Base
{
    public function getZt($params)
    {
        $params['a'] =  isset($params['ztid']) ? 'infolist' :$params['type'];
        $params['c'] = $this->getClassInfo(__CLASS__);
        $data = $this->makeData($params);
        return $data;
    }
    
}