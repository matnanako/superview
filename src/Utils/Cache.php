<?php

namespace SuperView\Utils;

use Closure;
use Illuminate\Support\Facades\Cache as CacheFacade;

class Cache extends CacheFacade
{

    private static $cacheMinutes; // 自定义缓存时间

    private static $keepCacheTime; // 是否保存自定义设置的缓存时间

    /**
     * 调用laravel Cache方法.
     * 
     * @return object
     */
    public static function __callStatic($method, $params) {
        // 给'remember', 'sear', 'rememberForever'三种方法加上清缓存操作，
        // 如果要添加方法，确保被添加的方法的第一个参数为$key.
        if (in_array($method, ['remember', 'sear', 'rememberForever'])) {
            self::clearCache($params[0]);
        }
        return parent::$method(...$params);
    }

    /**
     * Get cache time.
     * 
     * @return object
     */
    public static function clearCache($key)
    {
        // 如果参数设置了刷新缓存配置则清除缓存
        if (!empty(\SConfig::get('refresh_cache'))) {
            parent::forget($key);
        }
    }

    /**
     * Get cache time.
     * 
     * @return object
     */
    public static function getCacheTime()
    {
        $cacheMinutes = empty(self::$cacheMinutes) ? \SConfig::get('cache_minutes') : self::$cacheMinutes;

        // 如果不需要保持设置，使用之后清空self::$cacheMinutes。
        if (!self::$keepCacheTime) {
            self::$cacheMinutes = 0;
        }

        return $cacheMinutes;
    }

    /**
     * Set cache time.
     * 
     * @param  string  $minutes
     * @param  array  $keepCacheTime 是否保持设置
     * @return object
     */
    public static function setCacheTime($cacheMinutes, $keepCacheTime)
    {
        self::$cacheMinutes = $cacheMinutes;
        self::$keepCacheTime = $keepCacheTime;
    }


    /**
     * Get cache key.
     * 
     * @return object
     */
    public static function getCachekey($model, $method, $params, $cacheMinutes)
    {
        if (empty($cacheMinutes)) {
            $cache_key = false;
        } else {
            $cache_key = $model->makeCacheKey($method, $params);
        }

        return $cache_key;
    }

}
