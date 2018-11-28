<?php

namespace SuperView\Models;

class CategoryModel extends BaseModel
{

    /**
     * Use static variable type for we can read cache only one time during one request.
     */
    private static $categories;

    /**
     * 所有方法依赖all
     * Get categories list and store in cache.
     *
     * @return array
     */
    public function all()
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

        return empty(self::$categories[$classid]) ? null : self::$categories[$classid];
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
        if (empty($category)) {
            return [];
        }
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
     * 获取分类的下一级分类.
     *
     * @param  int  $classid
     * @return boolean | array
     */
    public function children($classid = 0, $limit = 0)
    {
        if (empty($classid)) {
            return false;
        }

        $category = $this->info($classid);
        if (empty($category) || !isset($category['children'])) {
            return [];
        }

        $children = $category['children'];
        $this->addCategoryUrl($children);

        if (empty($limit)) {
            return $children;
        }

        // 需要生成分页
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
        if (empty($category)) {
            return [];
        }
        $parent_category_id = $category['bclassid'];
        // 如果是顶级分类则直接获取所有顶级分类
        if ($parent_category_id == 0) {
            $brothers = $this->getChannels();
        } else {
            // 否则获取父分类的子分类
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
        if (empty($category)) {
            return [];
        }
        $categories = [$category];

        // 查找当前分类的所有父分类
        while (isset($category['bclassid']) && $category['bclassid'] != 0) {
            $category = $this->info($category['bclassid']);
            if (empty($category)) {
                break;
            }
            $categories[] = $category;
        }

        $this->addCategoryUrl($categories);

        // 反序，让父类在数组的顶部
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

        // 未设置分类ID则从顶级频道开始查找
        if (empty($classid)) {
            $categories = $this->getChannels();
        } else {
            $categories = $this->children($classid);
        }

        $matches = [];
        $this->searchCategoryByName($categories, $name, $matches);

        return $matches;
    }

    /**
     * 根据class_url配置获取分类页url.
     *
     * @return string
     */
    public function categoryUrl($classid = 0, $page = 1)
    {
        $classUrlTpl = \SConfig::get('class_url');
        $category = $this->info($classid);
        if (empty($category)) {
            return '';
        }
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
    private function searchCategoryByName($categories, $name, &$matches)
    {
        foreach ($categories as $key => $category) {
            if (strpos($category['classname'], $name) !== false) {
                $matches[] = $category;
            }
            if (isset($category['children'])) {
                $this->searchCategoryByName($category['children'], $name, $matches);
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
     * 为分类添加分页页url.
     * url未存入缓存, 确保可同时使用多个url规则.
     *
     * @return boolean
     */
    private function addCategoryUrl(&$categories)
    {
        if (empty($categories)) {
            return false;
        }
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
    public function makeCacheKey($method, $params = [] ,$model='')
    {
        return false;
    }
}
