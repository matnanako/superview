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
        $categories = \SCache::sear($cache_key, function()  {
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
     * Get category brother.
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
     * Get category breadcrumb.
     * 
     * @return boolean | array
     */
    public function breadcrumbs($classid)
    {
        if (empty($classid)) {
            return false;
        }

        $category = $this->info($classid);
        $categories = [$category];

        while (isset($category['bclassid']) && $category['bclassid'] != 0) {
            $parentCategory = $this->info($category['bclassid']);
            $category = $parentCategory;
            $categories[] = $category;
        }

        $class_url = \SConfig::get('class_url');
        foreach ($categories as &$category) {
            $category['classurl'] = str_replace(['{channel}','{classname}','{classid}'],
                [$category['channel'], $category['bname'], $category['classid']],
                $class_url);
        }

        // 反序存储，让父类在数组的顶部
        $breadcrumbs = array_reverse($categories);

        return $breadcrumbs;
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
        $channels = \SCache::sear($cache_key, function() {
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