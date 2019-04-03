<?php

namespace SuperView\Dal\Api;

/**
* Content Dal.
*/
class Content extends Base
{

     /**
     * 排名因子枚举
     */
    private static $periods = [
        'day', 'week', 'month', 'all'
    ];

    /**
     * 排序因子枚举
     */
    private static $orderKeys = [
        'newstime', 'newstimeasc', 'allhits', 'monthhits', 'weekhits', 'id'
    ];

    /**
     * 内容详情
     * @return boolean | array
     */
    public function getInfo($id = 0)
    {
        if (intval($id) <= 0) {
            return false;
        }
        $params = [
            'id' => intval($id)
        ];

        return $this->getData('info', $params);
    }

    /**
     * 最新列表
     * @return boolean | array
     */
    public function getRecentList($classid, $page, $limit, $isPic)
    {
        $params = [
            'classid' => ($classid),
            'page'    => intval($page),
            'limit'   => intval($limit),
            'ispic'   => intval($isPic),
        ];
        return $this->getData('recent', $params);
    }

    /**
     * 排名列表
     * @return boolean | array
     */
    public function getRankList($classid, $page, $limit, $isPic, $period)
    {
        //取消时间段的验证，防止是数组
//        if (!in_array($period, self::$periods)) {
//            return false;
//        }
        $params = [
            'classid' => ($classid),
            'page'    => intval($page),
            'limit'   => intval($limit),
            'ispic'   => intval($isPic),
            'rank'    => $period,
        ];

        return $this->getData('rank', $params);
    }

    /**
     * 推荐信息列表
     * @return boolean | array
     */
    public function getLevelList($type, $classid, $page, $limit, $isPic, $level, $order)
    {
        if (!$this->isValidOrder($order) || !$this->isValidLevel($level)) {
            return false;
        }

        if (empty($type) || !in_array($type, ['good', 'top', 'firsttitle'])) {
            return false;
        }

        $params = [
            'level'   => ($level),
            'classid' => ($classid),
            'page'    => intval($page),
            'limit'   => intval($limit),
            'ispic'   => intval($isPic),
            'order'   => $order,
        ];
        return $this->getData($type, $params);
    }

    /**
     * 今日更新列表
     * @return boolean | array
     */
    public function getTodayList($classid, $page, $limit, $isPic, $order)
    {
        if (!$this->isValidOrder($order)) {
            return false;
        }

        $params = [
            'classid' => ($classid),
            'page'    => intval($page),
            'limit'   => intval($limit),
            'ispic'   => intval($isPic),
            'order'   => $order,
        ];
        return $this->getData('today', $params);
    }

    /**
     * 时间段列表
     * @return boolean | array
     */
    public function getIntervalList($startTime, $endTime, $classid, $page, $limit, $isPic, $order)
    {
        if (!$this->isValidOrder($order)) {
            return false;
        }

        if($startTime > $endTime || $startTime < 0 || $endTime < 0) {
            return false;
        }

        $params = [
            'start'   => intval($startTime),
            'end'     => intval($endTime),
            'classid' => ($classid),
            'page'    => intval($page),
            'limit'   => intval($limit),
            'ispic'   => intval($isPic),
            'order'   => $order,
        ];
        return $this->getData('interval', $params);
    }

    /**
     * 时间段列表
     * @return boolean | array
     */
    public function getListByTitle($title, $classid, $page, $limit, $isPic, $order)
    {
        if (!$this->isValidOrder($order)) {
            return false;
        }

        $params = [
            'title'   => $title,
            'classid' => ($classid),
            'page'    => intval($page),
            'limit'   => intval($limit),
            'ispic'   => intval($isPic),
            'order'   => $order,
        ];
        return $this->getData('title', $params);
    }

    /**
     * 信息相关列表
     * @return boolean | array
     */
    public function getRelatedList($id, $page, $limit, $isPic, $order)
    {
        if (!$this->isValidOrder($order)) {
            return false;
        }

        $params = [
            'id'    => ($id),
            'page'  => intval($page),
            'limit' => intval($limit),
            'ispic' => intval($isPic),
            'order' => $order,
        ];
        return $this->getData('related', $params);
    }

    /**
     * TAG信息列表
     * @return boolean | array
     */
    public function getListByTag($tag,$classid, $page, $limit, $isPic, $order)
    {
        if (!$this->isValidOrder($order)) {
            return false;
        }

        $params = [
            'tag'   => $tag,
            'classid' => $classid,
            'page'  => intval($page),
            'limit' => intval($limit),
            'ispic' => intval($isPic),
            'order' => $order,
        ];
        return $this->getData('tag', $params);
    }

    /**
     * 信息所属专题列表
     * @return boolean | array
     */
    public function getInfoTopics($id, $limit)
    {
        $params = [
            'id'    => ($id),
            'limit' => intval($limit),
        ];
        return $this->getData('infoTopics', $params);
    }


    /**
     * 专题信息列表
     * @return boolean | array
     */
    public function getListByTopicId($topicId, $page, $limit)
    {
        $params = [
            'ztid'  => ($topicId),
            'page'  => intval($page),
            'limit' => intval($limit),
        ];
        return $this->getData('special', $params);
    }

    /**
     * 信息搜索列表
     * @return boolean | array
     */
    public function getListByKeyword($str, $classid, $page, $limit, $isPic, $order)
    {
        $params = [
            'str'     => $str,
            'classid' => ($classid),
            'page'    => intval($page),
            'limit'   => intval($limit),
            'ispic'   => intval($isPic),
            'order'   => $order,
        ];
        return $this->getData('search', $params);
    }

    /**
     * 信息搜索列表：根据指定字段指定值
     * @return boolean | array
     */
    public function getListByFieldValue($field,$value, $classid, $page, $limit, $isPic, $order)
    {
        $params = [
            'field'   => $field,
            'value'   => $value,
            'classid' => ($classid),
            'page'    => intval($page),
            'limit'   => intval($limit),
            'ispic'   => intval($isPic),
            'order'   => $order,
        ];
        return $this->getData('match', $params);
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
    public function near($id,$limit,$classid,$equal,$isPic,$order)
    {
        $params = [
            'id'   => $id,
            'limit'   => $limit,
            'classid' => ($classid),
            'equal'    => intval($equal),
            'ispic'   => intval($isPic),
            'order'   => $order,
        ];
        return $this->getData('near', $params);
    }


    /**
     * 获取数量统计
     * @return boolean | array
     */
    public function getCount($period, $classid)
    {
        if (!in_array($period, self::$periods)) {
            return false;
        }
        $params = [
            'interval' => $period,
            'classid'  => ($classid)
        ];

        return $this->getData('count', $params);
    }


    /**
     * 检查level参数是否正确
     * @param int $level 等级
     * @return boolean
     */
    public function isValidLevel($level)
    {
        return 0 <= intval($level) && intval($level) <= 9;
    }

    /**
     * 检查order参数是否正确
     * @param string $order 排序因子
     * @return boolean
     */
    public function isValidOrder($order)
    {
        return empty($order) || in_array($order, self::$orderKeys);
    }

    /**
     * 定制接口 获取相关软件 （此方法仅学院可用）
     *
     * @param $id
     * @param $limit
     * @return array|bool|mixed
     */
    public function getInfoRelated($id, $channel, $classid, $limit)
    {
        $params = [
            'id'     => $id,
            'classid' => $classid,
            'channel' => $channel,
            'limit' => $limit,
        ];
        return $this->getData('infoRelated', $params);
    }

    /**
     * 根据Tag词查询title定制方法
     *
     * @param $str
     * @param int $classid
     * @param int $limit
     * @param int $isPic
     * @param string $order
     * @return mixed
     */
    public function getRelatedSearch($str, $classid, $limit, $isPic, $order)
    {
        $params = [
            'str'     => $str,
            'classid' => $classid,
            'limit' => $limit,
            'ispic'   => intval($isPic),
            'order'   => $order,

        ];
        return $this->getData('relatedSearch', $params);

    }

    /**
     * 根据字段搜索
     *
     * @param $field
     * @param $value
     * @param $classid
     * @param $page
     * @param $limit
     * @param $ispic
     * @param $order
     * @return array|bool|mixed
     */
    public function getExactMatch($field,$value, $classid, $page, $limit, $ispic, $order)
    {
        $params = [
            'value'   => $value,
            'field'   => $field,
            'classid' => ($classid),
            'page'    => intval($page),
            'limit'   => intval($limit),
            'ispic'   => intval($ispic),
            'order'   => $order,
        ];
        return $this->getData('exactMatch', $params);
    }

    /**
     * 定制版 信息相关列表
     *
     * @param $model
     * @param $id
     * @param $page
     * @param $limit
     * @param $isPic
     * @param $order
     * @return array|bool|mixed
     */
    public function getRelatedModel($model, $id, $page, $limit, $isPic, $order)
    {
        if (!$this->isValidOrder($order)) {
            return false;
        }
        $params = [
            'model' => $model,
            'id'    => ($id),
            'page'  => intval($page),
            'limit' => intval($limit),
            'ispic' => intval($isPic),
            'order' => $order,
        ];
        return $this->getData('relatedModel', $params);
    }
}
