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
    private static $rankPeriods = [
        'day', 'week', 'month', 'all'
    ];

    /**
     * 排序因子枚举
     */
    private static $orderKeys = [
        'newstime', 'newstimeasc', 'allhits', 'monthhits', 'weekhits', 'id'
    ];

    /**
     * 查询内容详情
     * @param int $id 信息ID
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

    public function getRecentList($classid, $page, $limit, $is_pic)
    {
        $params = [
            'classid' => intval($classid),
            'page'    => intval($page),
            'limit'   => intval($limit),
            'ispic'   => intval($is_pic),
        ];
        return $this->getData('recent', $params);
    }

    public function getRankList($classid, $page, $limit, $is_pic, $period)
    {
        if (!in_array($period, self::$rankPeriods)) {
            return false;
        }
        $params = [
            'classid' => intval($classid),
            'page'    => intval($page),
            'limit'   => intval($limit),
            'ispic'   => intval($is_pic),
            'rank'    => $period,
        ];

        return $this->getData('rank', $params);
    }

    /**
     * 推荐自定义列表
     * @return boolean | array
     */
    public function getCustomList($type, $classid, $page, $limit, $is_pic, $level, $order = '')
    {
        if (!$this->isValidOrder($order) || !$this->isValidLevel($level)) {
            return false;
        }

        if (empty($type) || !in_array($type, ['good', 'top', 'firsttitle'])) {
            return false;
        }

        $params = [
            'level'   => intval($level),
            'classid' => intval($classid),
            'page'    => intval($page),
            'limit'   => intval($limit),
            'ispic'   => intval($is_pic),
            'order'   => $order,
        ];
        return $this->getData($type, $params);
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

}