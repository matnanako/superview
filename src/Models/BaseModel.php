<?php

namespace SuperView\Models;

use SuperView\Dal\Dal;

class BaseModel
{
    protected $dal;

    protected static $cache_param_keys = [];

    public function __construct()
    {
        $this->dal = new Dal();
    }

    /**
     * Generate cache key by params.
     * 
     * @return string
     */
    public function makeCacheKey($method, $params = [])
    {
        $cache_keys = [];
        foreach (static::$cache_param_keys as $key => $default) {
            isset($params[$key]) ? $cache_keys[$key] = $params[$key] : '';
        }
        return md5(\Config::get('api_base_url') . get_class($this) . ':' . $method . http_build_query($cache_keys));
    }

    /**
     * Set params default value according config.
     * 
     * @param  int  $id
     * @return void
     */
    public function setDefaultParams(&$params)
    {
        foreach (static::$cache_param_keys as $key => $default) {
            if (!isset($params[$key])) {
                $params[$key] = $default;
            }
        }
    }

    /*
    * method for checkParams 
    */
    public function checkParams($params=[])
    {
        $checkParams['classid'] = empty($params['classid']) ||(is_integer($params['classid']))?'true': 'false';
        $checkParams['page'] = empty($params['page']) ||(is_integer($params['page']))?'true': 'false';
        $checkParams['limit'] = empty($params['limit']) ||(is_integer($params['limit']))?'true': 'false';
        $checkParams['ispic'] = empty($params['ispic']) ||(is_integer($params['ispic']))?'true': 'false'; 
        $checkParams['start'] = empty($params['start']) ||(is_integer($params['start']))?'true': 'false';
        $checkParams['end'] = empty($params['end']) ||(is_integer($params['end']))?'true': 'false';
        $checkParams['level'] = empty($params['level']) ||(is_integer($params['level']))?'true': 'false';
        $checkParams['wordsep'] = empty($params['wordsep']) ||(is_integer($params['wordsep']))?'true': 'false';  
        $checkParams['order'] = empty($params['order']) ||(is_string($params['order']))?'true': 'false'; 
        $checkParams['match'] = empty($params['match']) ||(is_string($params['match']))?'true': 'false'; 
        foreach ($checkParams as $key => $value) {
            // echo $value;die('22');
            if ($value == 'false') {
                dd('wrong prams type');
            }
        }   
    }
}