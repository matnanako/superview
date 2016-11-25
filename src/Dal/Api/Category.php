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
        $this->makeTrees($categories);

        // Generate normative model data.
        foreach ($categories as $key => &$category) {
            $this->getChannel($categories, $category['classid']);
        }
        return $categories;
    }

    /**
     * Set top category id.
     * 
     * @param  int  $classid
     * @return array
     */
    private function getChannel(&$categories, $classid)
    {
        $category =& $categories[$classid];

        // 如果在前面的递归中已经设置了当前分类的channel_id，不需要再处理。
        if (!isset($category['channel_id'])) {
            if ($category['bclassid'] == 0) {
                $category['channel_id'] = 0;
                $category['channel'] = $category['bname'];
            } else {
                $category['channel_id'] = $this->getChannel($categories, $category['bclassid']);
                $category['channel'] = $category['bname'];
            }
        }
    }

    private function makeTrees(&$categories) {
        foreach ($categories as $key => $category) {
            $parentId = $category['bclassid'];
            if ($parentId != 0 && isset($categories[$parentId])) {
                $categories[$parentId]['children'][] =& $categories[$key];
            }
        }
    }

}
