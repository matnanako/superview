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
        $this->addListInfo($data['list']);
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
        $this->addListInfo($data['list']);
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
        $this->addListInfo($data['list']);
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
        $this->addListInfo($data['list']);
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
        $this->addListInfo($data['list']);
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
        $this->addListInfo($data['list']);
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
        $class_url = \Config::get('class_url');
        $info_url = \Config::get('info_url');
        foreach ($list as $key => &$value) {
            $category = $categoryModel->info($value['classid']);
            $value['class'] = $category['classname'];
            $value['classurl'] = str_replace(['{channel}','{classname}','{classid}'],
                [$category['channel'], $category['bname'], $value['classid']],
                $class_url);
            $value['infourl'] = str_replace(['{channel}','{id}'], [$category['channel'],$value['id']], $info_url);
        }
    }

}
