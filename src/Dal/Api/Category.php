<?php

namespace SuperView\Dal\Api;

/**
* Category Dal.
*/
class Category extends Base
{

    public function getList()
    {
        $params['a'] = 'all';
        $categories = $this->getData($params);

        // Generate normative model data.
        foreach ($categories as $key => &$category) {
            $category['category_id'] = $category['classid'];
            $category['p_category_id'] = $category['bclassid'];
            unset($category['classid'], $category['bclassid']);
        }
        return $categories;
    }
}