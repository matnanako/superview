<?php

namespace SuperView\Models;

class ContentModel extends BaseModel
{

    /**
     * Get latest list.
     * 
     * @return array
     */
    public function info($id = 0)
    {
        $data = $this->dal['content:' . $this->virtualModel]->getInfo($id);
        if (isset($data['list'])) {
            $this->addListInfo($data['list']);
        } else {
            $data = [];
        }

        return $data;
    }

    /**
     * Get latest list.
     * 
     * @return boolean | array
     */
    public function recent($classid = 0, $page = 1, $limit = 20, $is_pic = 0)
    {
        $data = $this->dal['content:' . $this->virtualModel]->getRecentList($classid, $page, $limit, $is_pic);
        if (isset($data['list'])) {
            $this->addListInfo($data['list']);
        } else {
            $data = [];
        }

        return $data;
    }

    /**
     * Get rank list.
     * 
     * @return array
     */
    public function rank($classid = 0, $page = 1, $limit = 20, $is_pic = 0, $period = 'all')
    {
        $data = $this->dal['content:' . $this->virtualModel]->getRankList($classid, $page, $limit, $is_pic, $period);
        if (isset($data['list'])) {
            $this->addListInfo($data['list']);
        } else {
            $data = [];
        }

        return $data;
    }

    /**
     * Get good list.
     * 
     * @return boolean | array
     */
    public function good($classid = 0, $page = 1, $limit = 20, $is_pic = 0, $level = 0, $order = 'newstime')
    {
        $data = $this->dal['content:' . $this->virtualModel]->getCustomList('good', $classid, $page, $limit, $is_pic, $level, $order);
        if (isset($data['list'])) {
            $this->addListInfo($data['list']);
        } else {
            $data = [];
        }

        return $data;
    }

    /**
     * Get top list.
     * 
     * @return boolean | array
     */
    public function top($classid = 0, $page = 1, $limit = 20, $is_pic = 0, $level = 0, $order = 'newstime')
    {
        $data = $this->dal['content:' . $this->virtualModel]->getCustomList('top', $classid, $page, $limit, $is_pic, $level, $order);
        if (isset($data['list'])) {
            $this->addListInfo($data['list']);
        } else {
            $data = [];
        }

        return $data;
    }

    /**
     * Get firsttitle list.
     * 
     * @return boolean | array
     */
    public function firsttitle($classid = 0, $page = 1, $limit = 20, $is_pic = 0, $level = 0, $order = 'newstime')
    {
        $data = $this->dal['content:' . $this->virtualModel]->getCustomList('firsttitle', $classid, $page, $limit, $is_pic, $level, $order);
        if (isset($data['list'])) {
            $this->addListInfo($data['list']);
        } else {
            $data = [];
        }

        return $data;
    }


    /**
     * 添加列表包含信息：分类信息、url.
     * 
     * @return array
     */
    private function addListInfo(&$list)
    {
        if (empty($list)) {
            return false;
        }

        $categoryModel = CategoryModel::getInstance();
        $class_url = \SConfig::get('class_url');
        $info_url = \SConfig::get('info_url');
        foreach ($list as $key => &$value) {
            $category = $categoryModel->info($value['classid']);
            $value['classname'] = $category['classname'];
            $value['classurl'] = str_replace(['{channel}','{classname}','{classid}'],
                [$category['channel'], $category['bname'], $value['classid']],
                $class_url);
            $value['infourl'] = str_replace(['{channel}','{classname}','{classid}','{id}'], [$category['channel'],$category['bname'],$value['classid'],$value['id']], $info_url);
        }
    }

}
