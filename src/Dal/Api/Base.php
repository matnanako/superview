<?php

namespace SuperView\Dal\Api;

use SuperView\Utils\Api;

/**
* Base Dal.
*/
class Base
{
    protected $api;
    private $virtual_model;

    public function __construct($virtual_model)
    {
        $this->api = new Api();
        $this->virtual_model = $virtual_model;
    }

    public function getData($params)
    {
        $params['c'] = $this->virtual_model;
        $data = $this->api->get($params);
        if (isset($data['data'])) {
            return $data['data'];
        } else {
            throw new \SuperView\Exceptions\BaseException("SuperView Error!");
        }
    }

}