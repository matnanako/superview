<?php

namespace SuperView\Dal;

use ArrayAccess;

/**
* Dal.
*/
class Dal implements ArrayAccess
{

    private static $dals;
    
     public function offsetGet($offset)
    {
        $dals = \SConfig::get('dals');
        if (!isset(self::$dals[$offset])) {
            if (strstr($offset, 'content:')) {
                $dal_key = 'content';
                $virtual_dal = substr($offset, strpos($offset, ":") + 1);
            } else {
                $dal_key = $virtual_dal = $offset;
            }
            if (isset($dals[$dal_key]) && class_exists($dals[$dal_key])) {
                self::$dals[$dal_key] = new $dals[$dal_key]($virtual_dal);
            } else {
                return [];
            }
        }

        return self::$dals[$dal_key];
    }

    public function offsetExists($offset) {}

    public function offsetSet($offset, $value) {}

    public function offsetUnset($offset) {}

}