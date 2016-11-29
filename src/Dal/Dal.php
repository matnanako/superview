<?php

namespace SuperView\Dal;

use ArrayAccess;

/**
* Dal.
*/
class Dal implements ArrayAccess
{

    private $dals;

    private static $instance;
    
    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    
    public function offsetGet($offset)
    {
        $dals = \SConfig::get('dals');

        // 将content:soft转换成dalKey为content,virtualDal为soft
        if (strstr($offset, 'content:')) {
            $dalKey = 'content';
            $virtualDal = substr($offset, strpos($offset, ":") + 1);
        } else {
            $dalKey = $virtualDal = $offset;
        }

        if (!isset($this->dals[$dalKey])) {
            if (isset($dals[$dalKey]) && class_exists($dals[$dalKey])) {
                $this->dals[$dalKey] = new $dals[$dalKey]($virtualDal);
            } else {
                return false;
            }
        }

        return $this->dals[$dalKey];
    }

    public function offsetExists($offset) {}

    public function offsetSet($offset, $value) {}

    public function offsetUnset($offset) {}

}