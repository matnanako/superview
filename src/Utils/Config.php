<?php

namespace SuperView\Utils;

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
