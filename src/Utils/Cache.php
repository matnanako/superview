<?php

namespace SuperView\Utils;

use Illuminate\Support\Facades\Cache as CacheFacade;

class Cache extends CacheFacade
{

    private static $cacheMinutes; // 自定义缓存时间

    private static $keepCacheTime; // 是否保存自定义设置的缓存时间

    /**
     * 调用laravel Cache方法.
     * @param $method
     * @param  $params
     * @return object
     */
    public static function __callStatic($method, $params)
    {
        // 给'remember', 'sear', 'rememberForever'三种方法加上清缓存操作,
        // 如果要添加方法，确保参数$params[0]为$key.
        if (in_array($method, ['remember', 'sear', 'rememberForever'])) {
            self::clearCache($params[0]);
        }
        return parent::$method(...$params);
    }

    /**
     * clear cache.
     * @param string $key
     */
    public static function clearCache($key)
    {
        // 如果参数设置了刷新缓存配置则清除缓存
        if (!empty(\SConfig::get('refresh_cache')) && $key != ':TotalCategory') {
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
        // 如果未设置self::$cacheMinutes则使用默认配置
        $cacheMinutes = is_null(self::$cacheMinutes) ? \SConfig::get('cache_minutes') : self::$cacheMinutes;

        // 如果不需要保持设置，使用之后清空self::$cacheMinutes。
        if (!self::$keepCacheTime) {
            self::$cacheMinutes = null;
        }

        return $cacheMinutes;
    }

    /**
     * Set cache time.
     *
     * @param  string $cacheMinutes
     * @param  array $keepCacheTime 是否保持设置
     */
    public static function setCacheTime($cacheMinutes, $keepCacheTime)
    {
        self::$cacheMinutes = $cacheMinutes;
        self::$keepCacheTime = $keepCacheTime;
    }


    /**
     * 判断缓存时间并相应生成缓存KEY.
     * @param $model
     * @param $method
     * @param $params
     * @param $cacheMinutes
     * @return object
     */
    public static function getCachekey($model, $method, $params, $cacheMinutes)
    {
        if (empty($cacheMinutes)) {
            $cache_key = false;
        } else {
            $cache_key = $model->makeCacheKey($method, $params, $model);
        }
        return $cache_key;
    }


    public static function has($key)
    {
        return parent::has($key);
    }


}
