<?php

namespace SuperView\Models;

class TopicModel extends BaseModel
{

    /**
     * 专题列表
     */
    public function all($classid = 0, $topicCategoryId = 0, $page = 1, $limit = 20, $order = 'addtime')
    {
        $data = $this->dal['topic']->getList($classid, $topicCategoryId, $page, $limit, $order);
        return $data;
    }

    /**
     * 专题详情
     */
    public function info($id, $path = '')
    {
        if (empty($id)) {
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
        $data = $this->dal['topic']->getCategories();
        return $data;
    }


    // /**
    //  * 添加列表包含信息： url.
    //  * 
    //  * @return void
    //  */
    // private function addListInfo(&$data)
    // {
    //     if (!isset($data['list'])) {
    //         $data = [];
    //         return;
    //     }

    //     $categoryModel = CategoryModel::getInstance();
    //     $class_url = \SConfig::get('class_url');
    //     $info_url = \SConfig::get('info_url');
    //     foreach ($data['list'] as $key => &$value) {
    //         $category = $categoryModel->info($value['classid']);
    //         $value['classname'] = $category['classname'];
    //         $value['classurl'] = str_replace(['{channel}','{classname}','{classid}'],
    //             [$category['channel'], $category['bname'], $value['classid']],
    //             $class_url);
    //         $value['infourl'] = str_replace(['{channel}','{classname}','{classid}','{id}'], [$category['channel'],$category['bname'],$value['classid'],$value['id']], $info_url);
    //     }
    // }
}
