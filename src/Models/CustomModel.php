<?php
/**
 * 定制接口
 * User: pota
 * DateTime: 2018/12/12 10:53 AM
 */

namespace SuperView\Models;

use Illuminate\Support\Facades\Cache;
use SuperView\SuperView;
use SuperView\Utils\CacheKey;
use SuperView\Utils\Config;

class CustomModel extends BaseModel
{
    // 最后请求参数(去除已有缓存参数)
    protected $arguments = [];
    //请求参数适用于getOnly方法（所有参数）
    protected $allArgument =[];
    //所有参数
    protected $allCacheKey = [];

    /**
     * 获取单个列表 -依据方法顺序依次补足
     *
     * @param int $limit
     * @return mixed
     */
    public function getOnly($limit = 15)
    {
        $data = $this->dal['custom']->getList('getOnly', ['arguments' => $this->allArgument, 'limit' => $limit]);
        $this->addListInfo($data);
        //初始化
        $this->arguments = [];
        $this->allCacheKey = [];
        $this->allArgument = [];
        self::reset();
        return $data['list'];
    }

    /**
     * 暂未实现 获取所有调取的方法的列表  依据顺序返回
     *
     * @deprecated
     * @param int $limit
     * @return mixed
     */
    public function getList($limit = 15)
    {
        if($this->arguments){
            $data = $this->dal['custom']->getList('getList', ['arguments' => $this->arguments, 'limit' => $limit]);
            foreach($data AS &$value){
                $this->addListInfo($value);
                $value = $this->returnWithPage($value,$limit);
            }
            //生成缓存
            CacheKey::customMakeCache($data, $this->allCacheKey);
        }
        //读取缓存
        $data=CacheKey::getAllCache($this->allCacheKey);
        //初始化
        $this->arguments = [];
        $this->allCacheKey = [];
        $this->allArgument = [];
        self::reset();
        return $data;
    }

    /**
     * 返回模型类
     *
     * @param $modelAlias
     * @return mixed
     */
    private function getModel($modelAlias)
    {
        $models = Config::get('models');
        if (array_key_exists($modelAlias, $models)) {
            $model = $models[$modelAlias];
        } else {
            $model = $models['content'];
        }

        return $model;
    }

    /**
     * 获取方法参数
     *
     * @param $class
     * @param $name
     * @return \ReflectionParameter[]
     * @throws \ReflectionException
     */
    private function reflex($class, $name)
    {
        $ReflectionFunc = new \ReflectionMethod($class, $name);

        return $ReflectionFunc->getParameters();
    }

    /**
     * 魔术构造请求参数
     *
     * @param $method
     * @param $arguments
     * @return $this
     * @throws \ReflectionException
     */
    public function __call($method, $arguments)
    {
        $key = array_shift($arguments);
        $modelAlias = array_shift($arguments);
        $model = $this->getModel($modelAlias);
        if (!method_exists($model, $method)){
            throw new \Exception("调用不存在方法 类:{$model} 方法: {$method}");
        }
        $methodParam = $this->reflex($model, $method);
        $param = [];
        foreach ($methodParam AS $parameter){
            $position = $parameter->getPosition();
            $param[$parameter->name] = isset($arguments[$position]) ? $arguments[$position] : $parameter->getDefaultValue();
        }
        $this->arguments[$key] = [$key, $modelAlias, $method, $param];
        $this->allArgument[] = [$modelAlias, $method, $param];
        self::prepose($key,$modelAlias, $method, $param);
        return $this;
    }

    /**
     * 生成缓存的key
     *
     * @param $key
     * @param $modelAlias
     * @param $method
     * @param $param
     * @return $this
     */
    public function prepose($key,$modelAlias, $method, $param)
    {
        $cacheKey=CacheKey::custom($modelAlias, $method, $param);
        $this->allCacheKey[$key]=$cacheKey;
        if(CacheKey::haveCache($cacheKey)){
            unset($this->arguments[$key]);
        }

        return $this;
    }
}