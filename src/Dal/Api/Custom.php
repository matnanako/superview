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

    /**
     * 详情页定制接口
     *
     * @param $id
     * @param $baikelimit
     * @param $softlimit
     * @return array|bool
     */
    public function getSpecials($id,$baikelimit,$softlimit)
    {
        $params = [
            'id'    => $id,
            'baikelimit' => $baikelimit,
            'softlimit' => $softlimit,
        ];
        return $this->getData('specials', $params);
    }
}