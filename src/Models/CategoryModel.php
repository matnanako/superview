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
        $categories = \SCache::sear($cache_key, function () {
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
    public function info($classid = 0)
    {
        if (empty($classid)) {
            return false;
        }

        if (empty(self::$categories)) {
            self::$categories = $this->all();
        }

        return empty(self::$categories[$classid]) ? 0 : self::$categories[$classid];
    }

    /**
     * Get category final children.
     *
     * @param  int  $classid
     * @return boolean | array
     */
    public function finalChildren($classid = 0, $limit = 0)
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

        $this->addCategoryUrl($children);

        if (empty($limit)) {
            return $children;
        }

        $page = $this->getCurrentPage();
        $data['list'] = array_slice($children, ($page-1) * $limit, $limit);
        $data['count'] = count($children);

        return $this->returnWithPage($data, $limit);
    }

    /**
     * Get category  children.
     *
     * @param  int  $classid
     * @return boolean | array
     */
    public function children($classid = 0, $limit = 0)
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

        $children = self::$categories[$classid]['children'];
        $this->addCategoryUrl($children);

        if (empty($limit)) {
            return $children;
        }

        $page = $this->getCurrentPage();
        $data['list'] = array_slice($children, ($page-1) * $limit, $limit);
        $data['count'] = count($children);

        return $this->returnWithPage($data, $limit);
    }

    /**
     * Get category brother.
     *
     * @param  int  $classid
     * @return boolean | array
     */
    public function brothers($classid = 0)
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
    public function breadcrumbs($classid = 0)
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

        $this->addCategoryUrl($categories);

        // 反序存储，让父类在数组的顶部
        $breadcrumbs = array_reverse($categories);

        return $breadcrumbs;
    }

    /**
     * Get category breadcrumb.
     *
     * @return boolean | array
     */
    public function search($name = '', $classid = 0)
    {
        if (empty($name)) {
            return false;
        }

        if (empty(self::$categories)) {
            self::$categories = $this->all();
        }

        if (empty($classid)) {
            $categories = $this->getChannels();
        } else {
            $categories = $this->children($classid);
        }

        $matches = [];
        $this->searchCategoriesByName($categories, $name, $matches);

        return $matches;
    }

    /**
     * Get category page url.
     *
     * @return boolean | array
     */
    public function categoryUrl($classid = 0, $page = 1)
    {
        $classUrlTpl = \SConfig::get('class_url');
        $category = $this->info($classid);
        $classurl = str_replace(
            ['{channel}','{classname}','{classid}','{page}'],
            [$category['channel'], $category['bname'], $classid, $page],
            $classUrlTpl
        );
        return $classurl;
    }

    /**
     * 根据分类名称模糊查询分类列表.
     *
     * @return void
     */
    private function searchCategoriesByName($categories, $name, &$matches)
    {
        foreach ($categories as $key => $category) {
            if (strpos($category['classname'], $name) !== false) {
                $matches[] = $category;
            }
            if (isset($category['children'])) {
                $this->searchCategoriesByName($category['children'], $name, $matches);
            }
        }
    }

    /**
     * Get all top category.
     *
     * @return boolean | array
     */
    private function getChannels()
    {
        static $channels;

        if (empty($channels)) {
            // Store the cache forever.
            $cache_key = parent::makeCacheKey(__METHOD__);
            $channels = \SCache::sear($cache_key, function () {
                $categories = $this->all();
                $channels = [];
                foreach ($categories as $category) {
                    if ($category['bclassid'] == 0) {
                        $channels[] = $category;
                    }
                }
                return $channels;
            });
        }

        return $channels;
    }

    /**
     * Add url info.
     *
     * @return boolean | array
     */
    private function addCategoryUrl(&$categories)
    {
        $class_url = \SConfig::get('class_url');
        foreach ($categories as &$category) {
            $category['classurl'] = $this->categoryUrl($category['classid']);
        }
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
