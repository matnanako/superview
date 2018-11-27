<?php

namespace SuperView\Dal\Api;

use SuperView\Utils\Api;

/**
* Base Dal.
*/
class Base
{
    protected $api;
    protected $virtualDal;

    public function __construct($virtualDal)
    {
        $this->api = Api::getInstance();
        $this->virtualDal = $virtualDal;
    }

    public function getData($action, $params = [])
    {
        if (empty($this->virtualDal)) {
            return false;
        }

        $params['c'] = $this->virtualDal;
        $params['a'] = $action;


        $data = $this->api->get($params);


        if (isset($data['status']) && $data['status'] > 0) {
            return $data['data'];
        } else {
            return [];
        }
    }

}
