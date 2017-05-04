<?php

namespace SuperView\Models;

class ChipModel extends BaseModel
{

    /**
     * 动态碎片的软件列表
     */
    public function infolist($chipid = 0, $classid = 0, $limit = 0)
    {
        $page = $this->getCurrentPage();
        $data = $this->dal['chip']->getList($chipid, $classid, $page, $limit);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 根据chip id查询chip信息
     */
    public function info($chipid)
    {
        if ($chipid < 1) {
            return [];
        }
        $data = $this->dal['chip']->getInfo($chipid);
        return $data;
    }
}