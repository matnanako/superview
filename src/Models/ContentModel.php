<?php

namespace SuperView\Models;

class ContentModel extends BaseModel
{

    protected static $cache_param_keys = ['page' => 1, 'limit' => 10, 'category_id' => 0, 'is_pic' => 0, 'period' => ''];

    private $virtual_model;

    public function __construct()
    {
        static::$cache_param_keys = array_merge(static::$cache_param_keys, parent::$cache_param_keys);
        parent::__construct();
    }

    public function setVirtualModel($virtual_model)
    {
        $this->virtual_model = $virtual_model;
    }

    /**
     * Get latest list.
     * 
     * @return array
     */
    public function getList($params)
    {
        // Import variables into the current symbol table from params.
        $this->setDefaultParams($params);
        extract($params);

        $data = $this->dal['content:' . $this->virtual_model]->getList($category_id, $page, $limit, $is_pic, $period);
        return $data;
    }

}
