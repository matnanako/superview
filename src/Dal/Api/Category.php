<?php

namespace SuperView\Dal\Api;

/**
* Category Dal.
*/
class Category extends Base
{

    public function getList()
    {
        $categories = $this->getData('all');
        // 生成分类树结构
        $this->makeTrees($categories);

        // 设置频道数据.
        foreach ($categories as $key => &$category) {
            $this->setChannel($categories, $category['classid']);
        }
        return $categories;
    }

    /**
     * Set top category id.
     *
     * @param  int  $classid
     * @return array
     */
    private function setChannel(&$categories, $classid)
    {
        $category =& $categories[$classid];

        // 如果在前面的递归中已经设置了当前分类的channel_id，不需要再处理。
        if (!isset($category['channel_id'])) {
            if ($category['bclassid'] == 0) {
                $category['channel_id'] = $category['classid'];
                $category['channel'] = $category['bname'];
            } else {
                $parentCategory = $this->setChannel($categories, $category['bclassid']);
                $category['channel_id'] = $parentCategory['channel_id'];
                $category['channel'] = $parentCategory['channel'];
            }
        }
        return $category;
    }

    private function makeTrees(&$categories)
    {
        foreach ($categories as $key => $category) {
            $parentId = $category['bclassid'];
            if ($parentId != 0 && isset($categories[$parentId])) {
                $categories[$parentId]['children'][] =& $categories[$key];
            }
        }
    }

}
