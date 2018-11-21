<?php

namespace SuperView\Models;

class BannerModel extends BaseModel
{
    /**
     * @param $site
     * @param int $limit
     * @param string $order
     * @return array
     */
    public function lists($site, $limit = 3, $order = 'addtime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal['banner']->getList($site, $page, $limit, $order);
        return $this->returnWithPage($data, $limit);
    }

}
