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
    public function recent($classid = 0, $limit = 0, $isPic = 0)
    {
        $page = $this->getCurrentPage();
        $data = $this->dal()->getRecentList($classid, $page, $limit, $isPic);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 排名信息列表.
     */
    public function rank($rank = 'all', $classid = 0, $limit = 0, $isPic = 0)
    {
        $page = $this->getCurrentPage();
        $data = $this->dal()->getRankList($classid, $page, $limit, $isPic, $rank);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 推荐信息列表.
     */
    public function good($level = 0, $classid = 0, $limit = 0, $isPic = 0, $order = 'newstime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal()->getLevelList('good', $classid, $page, $limit, $isPic, $level, $order);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 置顶信息列表.
     */
    public function top($level = 0, $classid = 0, $limit = 0, $isPic = 0, $order = 'newstime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal()->getLevelList('top', $classid, $page, $limit, $isPic, $level, $order);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 头条信息列表.
     */
    public function firsttitle($level = 0, $classid = 0, $limit = 0, $isPic = 0, $order = 'newstime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal()->getLevelList('firsttitle', $classid, $page, $limit, $isPic, $level, $order);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 今日更新列表.
     */
    public function today($classid = 0, $limit = 0, $isPic = 0, $order = 'newstime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal()->getTodayList('today', $classid, $page, $limit, $isPic, $order);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 时间段列表.
     */
    public function interval($start = 0, $end = 0, $classid = 0, $limit = 0, $isPic = 0, $order = 'newstime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal()->getIntervalList($start, $end, $classid, $page, $limit, $isPic, $order);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * interval查询结果的总个数
     */
    public function intervalCount($start = 0, $end = 0, $classid = 0, $limit = 0, $isPic = 0, $order = 'newstime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal()->getIntervalList($start, $end, $classid, $page, $limit, $isPic, $order);
        if(empty($data['count'])){
            return -1;
        }

        return $data['count'];
    }

    /**
     * 相同标题信息列表.
     */
    public function title($title = '', $classid = 0, $limit = 0, $isPic = 0, $order = 'newstime')
    {
        if (empty($title)) {
            return false;
        }
        $page = $this->getCurrentPage();
        $data = $this->dal()->getListByTitle($title, $classid, $page, $limit, $isPic, $order);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 信息相关列表.
     */
    public function related($id = 0, $limit = 0, $isPic = 0, $order = 'newstime')
    {
        if (empty($id)) {
            return false;
        }
        $page = $this->getCurrentPage();
        $data = $this->dal()->getRelatedList($id, $page, $limit, $isPic, $order);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * TAG信息列表.
     */
    public function tag($tag = '',$classid = 0, $limit = 0, $isPic = 0, $order = 'newstime')
    {
        if (empty($tag)) {
            return false;
        }
        $page = $this->getCurrentPage();
        $data = $this->dal()->getListByTag($tag, $classid, $page, $limit, $isPic, $order);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 查询TAG信息总数
     */
    public function tagCount($tag = '', $limit = 0, $isPic = 0, $order = 'newstime')
    {
        if (empty($tag)) {
            return 0;
        }
        $page = $this->getCurrentPage();
        $data = $this->dal()->getListByTag($tag, $page, $limit, $isPic, $order);

        if(empty($data['count']) || $data['count'] < 0){
            return 0;
        }

        return $data['count'];
    }

    /**
     * 获取信息所属专题列表.
     */
    public function infoTopics($id = 0, $limit = 0)
    {
        if (empty($id)) {
            return false;
        }
        $data = $this->dal()->getInfoTopics($id, $limit);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 专题信息列表.
     */
    public function topic($topicId = 0, $limit = 0)
    {
        if (empty($topicId)) {
            return false;
        }
        $page = $this->getCurrentPage();
        $data = $this->dal()->getListByTopicId($topicId, $page, $limit);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 信息搜索列表.
     */
    public function search($str = '', $classid = 0, $limit = 0, $isPic = 0, $order = 'newstime')
    {
        if (empty($str)) {
            return false;
        }
        $page = $this->getCurrentPage();
        $data = $this->dal()->getListByKeyword($str, $classid, $page, $limit, $isPic, $order);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 信息搜索列表的总个数
     */
    public function searchCount($keyword = '', $classid = 0, $limit = 0, $isPic = 0, $order = 'newstime')
    {
        if (empty($keyword)) {
            return -1;
        }
        $page = $this->getCurrentPage();
        $data = $this->dal()->getListByKeyword($keyword, $classid, $page, $limit, $isPic, $order);

        if(empty($data['count'])){
            return -1;
        }

        return $data['count'];
    }

    /**
     * 信息搜索列表：根据指定字段指定值
     */
    public function match($field,$value, $classid = 0, $limit = 0, $isPic = 0, $order = 'newstime')
    {
        $field = trim($field);
        $value = trim($value);

        if (empty($field) || empty($value)) {
            return [];
        }
        $page = $this->getCurrentPage();
        $data = $this->dal()->getListByFieldValue($field,$value, $classid, $page, $limit, $isPic, $order);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 查询小于[等于]某id的$limit范围内的信息列表
     *
     * @param integer $id
     * @param integer $limit
     * @param integer $classid
     * @param integer $equal 默认为0小于$id，1小于等于$id
     *
     * @return array 符合查询条件的帝国cms的信息列表
     */
    public function near($id,$limit = 20,$classid = 0,$equal = 0,$ispic = 0,$order = 'newstime')
    {
        //参数检查
        if($id < 1 || $limit < 1){
            return [];
        }

        if($classid < 0){
            $classid = 0;
        }

        $equal = $equal == 1 ? 1 : 0;
        $ispic = $ispic == 1 ? 1 : 0;

        if(empty(trim($order))){
            $order = 'newstime';
        }

        $data = $this->dal()->near($id,$limit,$classid,$equal,$ispic,$order);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }


    /**
     * 数量统计.
     */
    public function count($period = 'all', $classid = 0)
    {
        $data = $this->dal()->getCount($period, $classid);
        return intval($data);
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

    /**
     * 定制接口 获取相关软件
     *
     * @param int $id
     * @param int $limit
     *
     */
    public function infoRelated($id = 0, $channel = '', $classid = 0, $limit = 15)
    {
        $data = $this->dal()->getInfoRelated($id, $channel, $classid, $limit);
        return $data;
    }

    /**
     * 根据Tag词查询title定制方法
     *
     * @param $channel
     * @param $str
     * @param int $classid
     * @param int $limit
     * @param int $isPic
     * @param string $order
     */
    public function relatedSearch($str, $classid = 0, $limit = 0, $isPic = 0, $order = 'newstime')
    {
        $data = $this->dal()->getRelatedSearch($str, $classid, $limit, $isPic, $order);
        $data = $this->addListInfo($data);
        return $data;
    }

    /**
     * 根据字段搜索
     *
     * @param $field
     * @param $value
     * @param int $classid
     * @param int $limit
     * @param int $ispic
     * @param string $order
     * @return array
     */
    public function exactMatch($field, $value, $classid = 0, $limit = 0, $ispic =0, $order = 'newstime')
    {

        $page = $this->getCurrentPage();
        $data = $this->dal()->getExactMatch($field,$value, $classid, $page, $limit, $ispic, $order);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 定制版 信息相关列表
     *
     * @param $model             $id对应的模型
     * @param int $id
     * @param int $limit
     * @param int $isPic
     * @param string $order
     * @return array|bool
     */
    public function relatedModel($model, $id = 0, $limit = 0, $isPic = 0, $order = 'newstime')
    {
        if (empty($id)) {
            return false;
        }
        $page = $this->getCurrentPage();
        $data = $this->dal()->getRelatedModel($model, $id, $page, $limit, $isPic, $order);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }
}
