<?php

namespace SuperView\Models;

class CategoryModel extends BaseModel
{

    /**
     * Use static variable type for we can read cache only one time during one request.
     */
    private static $categories;

    /**
     * Get categories list and store in cache.
     * 
     * @return array
     */
    private function all()
    {
        $cache_key = parent::makeCacheKey(__METHOD__);

        // Store the cache forever.
        $categories = \Cache::sear($cache_key, function()  {
            $categories = $this->dal['category']->getList();
            return $categories;
        });

        return $categories;
    }

    /**
     * Get category detail.
     * 
     * @param  int  $classid
     * @return boolean | array
     */
    public function info($classid)
    {
        if (empty($classid)) {
            return false;
        }

        if (empty(self::$categories)) {
            self::$categories = $this->all();
        }

        return self::$categories[$classid];
    }

    /**
     * Get category final children.
     * 
     * @param  int  $classid
     * @return boolean | array
     */
    public function finalChildren($classid)
    {
        if (empty($classid)) {
            return false;
        }
        $category = $this->info($classid);
        $children_ids = explode('|', trim($category['sonclass'], '|'));

        $children = [];
        foreach ($children_ids as $key => $child_id) {
            $child = $this->info($child_id);
            if (!empty($child)) {
                $children[$child_id] = $child;
            }
        }

        return $children;
    }

    /**
     * Get category  children.
     * 
     * @param  int  $classid
     * @return boolean | array
     */
    public function children($classid)
    {
        if (empty($classid)) {
            return false;
        }

        if (empty(self::$categories)) {
            self::$categories = $this->all();
        }

        if (!isset(self::$categories[$classid]['children'])) {
            return [];
        }

        return self::$categories[$classid]['children'];
    }

    /**
     * Get category brothers.
     * 
     * @param  int  $classid
     * @return boolean | array
     */
    public function brothers($classid)
    {
        if (empty($classid)) {
            return false;
        }
        $category = $this->info($classid);
        $parent_category_id = $category['bclassid'];
        if ($parent_category_id == 0) {
            $brothers = $this->getChannels();
        } else {
            $brothers = $this->children($parent_category_id);
        }

        // unset($brothers[$classid]); //剔除自己

        return $brothers;
    }

    /**
     * Get all top category.
     * 
     * @return boolean | array
     */
    public function getChannels()
    {

        $cache_key = parent::makeCacheKey(__METHOD__);

        // Store the cache forever.
        $channels = $categories = \Cache::sear($cache_key, function()  {
            $categories = $this->all();
            $channels = [];
            foreach ($categories as $category) {
                if ($category['bclassid'] == 0) {
                    $channels[] = $category;
                }
            }
            return $channels;
        });

        return $channels;
    }

    /**
     * Category类不需要统一缓存.
     * 
     * @return string
     */
    public function makeCacheKey($method, $params = [])
    {
        return false;
    }
}