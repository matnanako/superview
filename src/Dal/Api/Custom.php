<?php

namespace SuperView\Dal\Api;

class Custom extends Base
{
    /**
     * 请求列表
     *
     * @param $action
     * @param $params
     * @return array|bool
     */
    public function getList($action, $params)
    {
        return $this->getData($action, $params);
    }
}