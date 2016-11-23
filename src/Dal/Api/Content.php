<?php

namespace SuperView\Dal\Api;

/**
* Content Dal.
*/
class Content extends Base
{
    public function getList($parent_category_id, $page, $limit, $is_pic, $period)
    {
        if (empty($period)) {
            $params['a'] = 'recent';
        } else {
            $params['a'] = 'rank';
            $params['rank'] = $period;
        }

        return $this->getData($params);
    }
}