<?php

namespace SuperView\Dal\Api;

/**
 * Topic Dal.
 */
class Banner extends Base
{

    // 覆盖virtualDal.
    public function __construct($virtualDal)
    {
        parent::__construct($virtualDal);
        $this->virtualDal = 'banner';
    }

    /**
     * 专题列表
     * @return boolean | array
     */
    public function getList($site, $page, $limit, $order)
    {
        $params = [
            'site'  => intval($site),
            'page'  => intval($page),
            'limit' => intval($limit),
            'order' => $order,
        ];
        return $this->getData('lists', $params);
    }
}
