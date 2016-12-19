<?php

namespace SuperView\Utils;

/**
 * 配置类, 所有方法静态调用, 无需初始化配置.
 */
class Config
{
    protected static $configs = [];

    private static function init()
    {
        if (empty(self::$configs)) {
            self::import(require(__DIR__ . '/../../config/config.php'));
        }
    }

    public static function __callStatic($method, $params)
    {
        // 调用前检查有没有载入配置
        self::init();
        return self::$method(...$params);
    }

    private static function get($key = null)
    {
        // Return all configs if no key specified.
        if (empty($key)) {
            return self::$configs;
        }

        return isset(self::$configs[$key]) ? self::$configs[$key] : null;
    }

    private static function set($key, $value)
    {
        self::$configs[$key] = $value;
    }

    private static function import($configs)
    {
        self::$configs = array_merge(self::$configs, $configs);
    }
}
