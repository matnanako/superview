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
        return $data;
    }

    /**
     * 最新信息列表.
     */
    public function recent($classid = 0, $limit = 20, $isPic = 0)
    {
        $page = $this->getCurrentPage();
        $data = $this->dal()->getRecentList($classid, $page, $limit, $isPic);
        $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 排名信息列表.
     */
    public function rank($period = 'all', $classid = 0, $limit = 20, $isPic = 0)
    {
        $page = $this->getCurrentPage();
        $data = $this->dal()->getRankList($classid, $page, $limit, $isPic, $period);
        $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 推荐信息列表.
     */
    public function good($classid = 0, $limit = 20, $isPic = 0, $level = 0, $order = 'newstime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal()->getLevelList('good', $classid, $page, $limit, $isPic, $level, $order);
        $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 置顶信息列表.
     */
    public function top($classid = 0, $limit = 20, $isPic = 0, $level = 0, $order = 'newstime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal()->getLevelList('top', $classid, $page, $limit, $isPic, $level, $order);
        $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 头条信息列表.
     */
    public function firsttitle($classid = 0, $limit = 20, $isPic = 0, $level = 0, $order = 'newstime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal()->getLevelList('firsttitle', $classid, $page, $limit, $isPic, $level, $order);
        $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 今日更新列表.
     */
    public function today($classid = 0, $limit = 20, $isPic = 0, $order = 'newstime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal()->getTodayList('today', $classid, $page, $limit, $isPic, $order);
        $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 时间段列表.
     */
    public function interval($start = 0, $end = 0, $classid = 0, $limit = 20, $isPic = 0, $order = 'newstime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal()->getIntervalList($start, $end, $classid, $page, $limit, $isPic, $order);
        $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 相同标题信息列表.
     */
    public function title($title = '', $classid = 0, $limit = 20, $isPic = 0, $order = 'newstime')
    {
        if (empty($title)) {
            return false;
        }
        $page = $this->getCurrentPage();
        $data = $this->dal()->getListByTitle($title, $classid, $page, $limit, $isPic, $order);
        $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 信息相关列表.
     */
    public function related($id = 0, $limit = 20, $isPic = 0, $order = 'newstime')
    {
        if (empty($id)) {
            return false;
        }
        $page = $this->getCurrentPage();
        $data = $this->dal()->getRelatedList($id, $page, $limit, $isPic, $order);
        $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * TAG信息列表.
     */
    public function tag($tag = '', $limit = 20, $isPic = 0, $order = 'newstime')
    {
        if (empty($tag)) {
            return false;
        }
        $page = $this->getCurrentPage();
        $data = $this->dal()->getListByTag($tag, $page, $limit, $isPic, $order);
        $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 获取信息所属专题列表.
     */
    public function infoTopic($id = 0, $limit = 20)
    {
        if (empty($id)) {
            return false;
        }
        $data = $this->dal()->getInfoTopic($id, $limit);
        return $data;
    }

    /**
     * 专题信息列表.
     */
    public function topic($topicId = 0, $limit = 20)
    {
        if (empty($topicId)) {
            return false;
        }
        $page = $this->getCurrentPage();
        $data = $this->dal()->getListByTopic($topicId, $page, $limit);
        $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 信息搜索列表.
     */
    public function search($keyword = '', $classid = 0, $limit = 20, $isPic = 0, $order = 'newstime')
    {
        if (empty($keyword)) {
            return false;
        }
        $page = $this->getCurrentPage();
        $data = $this->dal()->getListByKeyword($keyword, $classid, $page, $limit, $isPic, $order);
        $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 添加列表包含信息：分类信息、url.
     * 
     * @return void
     */
    private function addListInfo(&$data)
    {
        if (!isset($data['list'])) {
            $data = [];
            return;
        }

        $categoryModel = CategoryModel::getInstance();
        $classUrl = \SConfig::get('class_url');
        $infoUrl  = \SConfig::get('info_url');
        foreach ($data['list'] as $key => &$value) {
            $category = $categoryModel->info($value['classid']);
            $value['classname'] = $category['classname'];
            $value['classurl'] = str_replace(['{channel}','{classname}','{classid}'],
                [$category['channel'], $category['bname'], $value['classid']],
                $classUrl);
            $value['infourl'] = str_replace(['{channel}','{classname}','{classid}','{id}'], [$category['channel'],$category['bname'],$value['classid'],$value['id']], $infoUrl);
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
