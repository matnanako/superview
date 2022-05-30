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

    /**
     * 新增多端方法plateform方法
     *
     * @param $softid
     * @param $model
     * @return array|false|mixed
     */
    public function plateform($softid, $model)
    {
        $params = [
            'softid' => $softid,
            'model' => $model
        ];
        return $this->getData('plateform', $params);
    }
}