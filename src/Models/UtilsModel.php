<?php

namespace SuperView\Models;

use SuperView\Utils\Page;

class UtilsModel extends BaseModel
{

    /**
     * 专题列表
     */
    public function friendLinks($type = 0, $classid = 0, $limit = 0)
    {
        $data = $this->dal['utils']->getFriendLinks($type, $classid, $limit);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 获取分页
     */
    public function renderPage($route, $total, $perPage, $currentPage = null, $simple = false, array $options = [])
    {
        $page = new Page($route, $total, $perPage, $currentPage, $simple, $options);
        return $page->render();
    }

}
