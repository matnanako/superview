<?php

namespace SuperView\Models;

class TopicModel extends BaseModel
{

    /**
     * 专题列表
     */
    public function index($zcid = 0, $classid = 0, $limit = 0, $order = 'addtime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal['topic']->getList($zcid, $classid, $page, $limit, $order);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * index查询结果的总个数
     */
    public function indexCount($zcid = 0, $classid = 0, $limit = 0, $order = 'addtime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal['topic']->getList($zcid, $classid, $page, $limit, $order);
        if(empty($data['count'])){
            return -1;
        }
        return $data['count'];
    }

    /**
     * 专题详情
     */
    public function info($id, $path = '')
    {
        if (empty($id) && empty($path)) {
            return false;
        }
        $data = $this->dal['topic']->getInfo($id, $path);
        return $data;
    }

    /**
     * 专题分类列表
     */
    public function categories()
    {
        $categories = $this->dal['topic']->getCategories();
        return $categories;
    }

    public function taginfo($ztid,$classid,$limit)
    {
        $page = $this->getCurrentPage();
        $data = $this->dal['topic']->taginfo($ztid, $classid, $page, $limit);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }
    /**
     * 详情页定制接口
     *
     * @param $id
     * @param string $model
     * @param int $baikelimit
     * @param int $softlimit
     * @return mixed
     */
    public function specials($id, $model = 'soft',$baikelimit = 5, $softlimit = 8)
    {
        $data = $this->dal['topic']->getSpecials($id, $model, $baikelimit, $softlimit);
        foreach ($data AS $key => $datum){
            $data[$key] = $this->addListInfo($datum);
        }
        return $data;

    }


    /**
     * 专题信息列表, 无法指定频道, 使用该方法获取该专题下的所有频道的内容.
     */
    public function superTopic($ztid = 0, $limit = 0)
    {
        if (empty($ztid)) {
            return false;
        }
        $page = $this->getCurrentPage();
        $data = $this->dal['topic']->getContentByTopicId($ztid, $page, $limit);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 天极定制接口
     *
     * @param $id
     * @param int $limit
     * @param int $baikelimit
     * @return mixed
     */
    public function topics($id, $limit = 5, $baikelimit = 30){
        $data = $this->dal['topic']->getTopics($id, $limit, $baikelimit);
        foreach ($data AS $key => $datum){
            $data[$key] = $this->addListInfo($datum);
        }
        return $data;
    }

    /**
     * 专题自定义查询
     *
     * @param $field
     * @param $value
     * @param int $classid
     * @param int $limit
     * @param string $order
     * @return array|string
     */
    public function match($field, $value, $classid = 0, $limit = 0, $order = 'addtime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal['topic']->getMatch($field,$value, $classid, $limit, $order, $page);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 获取游戏攻略标签
     *
     * @param $ztid
     * @param $id
     * @param $classid
     * @return mixed
     */
    public function label($ztid, $id, $classid = 0, $limit = 0)
    {
        return  $this->dal['topic']->getLabel($ztid, $id, $classid, $limit);
    }

    /**
     * 热门标签
     *
     * @param $ztid
     * @param int $limit
     * @return mixed
     */
    public function hotLabel($ztid, $limit = 0)
    {
        return $this->dal['topic']->getHotLabel($ztid, $limit);
    }

    /**
     * 专题标签下关联软件列表
     *
     * @param $ztid
     * @param $labelid
     * @param int $limit
     * @param int $order
     * @return mixed
     */
    public function labelList($ztid, $labelid = 0, $limit = 0, $order = 'newstime')
    {
        $page = $this->getCurrentPage();
        $data = $this->dal['topic']->getLabelList($ztid, $labelid, $limit, $page);
        $data = $this->addListInfo($data);
        return $this->returnWithPage($data, $limit);
    }

    /**
     * 所有标签
     *
     * @return mixed
     */
    public function allLabel()
    {
        return $this->dal['topic']->getAllLabel();
    }
}
