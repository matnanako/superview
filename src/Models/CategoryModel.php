<?php

namespace SuperView\Models;

class CategoryModel extends BaseModel
{

    protected static $cache_param_keys = [];

    /**
     * Get categories list and store in cache.
     * 
     * @return array
     */
    public function getList()
    {
        $cache_key = $this->makeCacheKey(__METHOD__);

        // Store the cache forever.
        $categories = \Cache::sear($cache_key, function()  {
            $categories = $this->dal['category']->getList();
            foreach ($categories as $category) {
                $mapping[$category['category_id']] = $category;
            }
            return $mapping;
        });

        return $categories;
    }

    /**
     * Get category detail.
     * 
     * @param  int  $id
     * @return array
     */
    public function getInfo($id)
    {
        if (empty($id)) {
            throw new \SuperView\Exceptions\BaseException("SuperView Error!");
        }
        $categories = $this->getList();
        return $categories[$id];
    }
}