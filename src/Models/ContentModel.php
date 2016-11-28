<?php

namespace SuperView\Models;

class ContentModel extends BaseModel
{

    /**
     * 获取信息详情.
     */
    public function info($id = 0)
    {
        $data = $this->dal()->getInfo($id);
        $this->addListInfo($data);

        return $data;
    }

    /**
     * 最新信息列表.
     */
    public function recent($classid = 0, $page = 1, $limit = 20, $isPic = 0)
    {
        $data = $this->dal()->getRecentList($classid, $page, $limit, $isPic);
        $this->addListInfo($data);

        return $data;
    }

    /**
     * 排名信息列表.
     */
    public function rank($classid = 0, $page = 1, $limit = 20, $isPic = 0, $period = 'all')
    {
        $data = $this->dal()->getRankList($classid, $page, $limit, $isPic, $period);
        $this->addListInfo($data);

        return $data;
    }

    /**
     * 推荐信息列表.
     */
    public function good($classid = 0, $page = 1, $limit = 20, $isPic = 0, $level = 0, $order = 'newstime')
    {
        $data = $this->dal()->getLevelList('good', $classid, $page, $limit, $isPic, $level, $order);
        $this->addListInfo($data);

        return $data;
    }

    /**
     * 置顶信息列表.
     */
    public function top($classid = 0, $page = 1, $limit = 20, $isPic = 0, $level = 0, $order = 'newstime')
    {
        $data = $this->dal()->getLevelList('top', $classid, $page, $limit, $isPic, $level, $order);
        $this->addListInfo($data);

        return $data;
    }

    /**
     * 头条信息列表.
     */
    public function firsttitle($classid = 0, $page = 1, $limit = 20, $isPic = 0, $level = 0, $order = 'newstime')
    {
        $data = $this->dal()->getLevelList('firsttitle', $classid, $page, $limit, $isPic, $level, $order);
        $this->addListInfo($data);

        return $data;
    }

    /**
     * 今日更新列表.
     */
    public function today($classid = 0, $page = 1, $limit = 20, $isPic = 0, $order = 'newstime')
    {
        $data = $this->dal()->getTodayList('today', $classid, $page, $limit, $isPic, $order);
        $this->addListInfo($data);

        return $data;
    }

    /**
     * 时间段列表.
     */
    public function interval($start = 0, $end = 0, $classid = 0, $page = 1, $limit = 20, $isPic = 0, $order = 'newstime')
    {
        $data = $this->dal()->getIntervalList($start, $end, $classid, $page, $limit, $isPic, $order);
        $this->addListInfo($data);

        return $data;
    }

    /**
     * 相同标题信息列表.
     */
    public function title($title = '', $classid = 0, $page = 1, $limit = 20, $isPic = 0, $order = 'newstime')
    {
        $data = $this->dal()->getListByTitle($title, $classid, $page, $limit, $isPic, $order);
        $this->addListInfo($data);

        return $data;
    }

    /**
     * 信息相关列表.
     */
    public function related($id = 0, $page = 1, $limit = 20, $isPic = 0, $order = 'newstime')
    {
        $data = $this->dal()->getRelatedList($id, $page, $limit, $isPic, $order);
        $this->addListInfo($data);

        return $data;
    }

    /**
     * TAG信息列表.
     */
    public function tag($tag = '', $page = 1, $limit = 20, $isPic = 0, $order = 'newstime')
    {
        $data = $this->dal()->getListByTag($tag, $page, $limit, $isPic, $order);
        $this->addListInfo($data);

        return $data;
    }

    /**
     * 获取信息所属专题列表.
     */
    public function infoTopic($id = 0, $limit = 20)
    {
        $data = $this->dal()->getInfoTopic($id, $limit);
        $this->addListInfo($data);

        return $data;
    }

    /**
     * 专题信息列表.
     */
    public function topic($topicId = 0, $page = 1, $limit = 20)
    {
        $data = $this->dal()->getListByTopic($topicId, $page = 1, $limit);
        $this->addListInfo($data);

        return $data;
    }

// 所属专题列表  speciallist    id  limit
// 专题信息列表 special    ztid page limit
// 信息搜索列表 search str classid page limit ispic order match wordsep


    /**
     * 添加列表包含信息：分类信息、url.
     * 
     * @return array
     */
    private function addListInfo(&$data)
    {
        if (!isset($data['list'])) {
            $data = [];
            return;
        }

        $categoryModel = CategoryModel::getInstance();
        $class_url = \SConfig::get('class_url');
        $info_url = \SConfig::get('info_url');
        foreach ($data['list'] as $key => &$value) {
            $category = $categoryModel->info($value['classid']);
            $value['classname'] = $category['classname'];
            $value['classurl'] = str_replace(['{channel}','{classname}','{classid}'],
                [$category['channel'], $category['bname'], $value['classid']],
                $class_url);
            $value['infourl'] = str_replace(['{channel}','{classname}','{classid}','{id}'], [$category['channel'],$category['bname'],$value['classid'],$value['id']], $info_url);
        }
    }

    /**
     * 获取dal模型.
     * 
     * @return object
     */
    private function dal()
    {
        return $this->dal['content:' . $this->virtualModel];
    }

}
