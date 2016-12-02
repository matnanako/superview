<?php

namespace SuperView\Models;

use SuperView\Utils\Page;

class UtilsModel extends BaseModel
{

    /**
     * 专题列表
     */
    public function friendLinks($type = 0, $classid = 0, $limit = 20)
    {
        $data = $this->dal['utils']->getFriendLinks($type, $classid, $limit);
        return $data;
    }

    /**
     * 专题列表
     */
    public function renderPage($route, $total, $perPage, $currentPage = null, array $options = [])
    {
        $page = new Page($route, $total, $perPage, $currentPage, $options);
        return $page->render();
    }

}
