<?php

namespace SuperView\Dal;

use ArrayAccess;

/**
* Dal.
*/
class Dal implements ArrayAccess
{

    private $dals;
    
     public function offsetGet($offset)
    {
        $dals = \Config::get('dals');
        $dals = $dals[\Config::get('default_dal')];
        if (!isset($this->dals[$offset])) {
            if (strstr($offset, 'content:')) {
                $model = 'content';
                $virtual_model = substr($offset, strpos($offset, ":") + 1);
            } else {
                $model = $virtual_model = $offset;
            }
            if (isset($dals[$model]) && class_exists($dals[$model])) {
                $this->dals[$model] = new $dals[$model]($virtual_model);
            } else {
                throw new \Exception("Dal \"$offset\" not found!");
            }
        }

        return $this->dals[$model];
    }

    public function offsetExists($offset) {}

    public function offsetSet($offset, $value) {}

    public function offsetUnset($offset) {}

}