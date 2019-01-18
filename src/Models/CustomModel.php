<?php
/**
 * 定制接口
 * User: pota
 * DateTime: 2018/12/12 10:53 AM
 */

namespace SuperView\Models;

use Illuminate\Support\Facades\Cache;
use SuperView\Utils\CacheKey;

/**
 *
 * @method top
 * Class CustomModel
 * @package SuperView\Models
 */
class CustomModel extends BaseModel
{
    // 最后请求参数(去除已有缓存参数)
    protected $arguments = [];
    //请求参数适用于getOnly方法（所有参数）
    protected $allArgument = [];
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
        $onlyKey = CacheKey::getOnlyCacheKey($this->allArgument);
        if (!empty(\SConfig::get('refresh_cache'))) {
            \SCache::clearCache($onlyKey);
        }
        $data = \SCache::remember($onlyKey, \SCache::getCacheTime(), function () use ($limit) {
            $data = $this->dal['custom']->getList('getOnly', ['arguments' => $this->allArgument, 'limit' => $limit]);
            $data = $this->addListInfo($data);
            return $data;
        });
        //初始化
        $this->initialize();
        return isset($data['list'])?$data['list']:$data;
    }

    /**
     * 暂未实现 获取所有调取的方法的列表  依据顺序返回   （$res 1指需要执行addlist方法   3代表自定义方法需要循环后走adddlist方法  2不需要走addlist方法）
     *
     * @deprecated
     * @param int $limit
     * @return mixed
     */
    public function getList($limit = 15)
    {
        if ($this->arguments) {
            $data = $this->dal['custom']->getList('getList', ['arguments' => $this->arguments, 'limit' => $limit]);
            foreach ($data AS $key => $value) {
                $res = CacheKey::getModelMethod($this->arguments[$key]);
                if ($res == 1) {
                    $data[$key] = $this->addListInfo($value);
                } elseif ($res == 3) {
                    foreach ($value as $k => $v) {
                        $data[$key][$k] = $this->addListInfo($v);
                    }
                }
                $data[$key] = $this->returnWithPage($data[$key], $limit);
            }
            //生成缓存
            CacheKey::customMakeCache($data, $this->allCacheKey, \SCache::getCacheTime());
        }
        //读取缓存
        $data = CacheKey::getAllCache($this->allCacheKey);
        //初始化
        $this->initialize();
        return $data;
    }

    /**
     * 魔术构造请求参数
     *
     * @param $method
     * @param $arguments
     * @return $this
     * @throws \Exception
     */
    public function __call($method, $arguments)
    {
        $res = CacheKey::customAll($method, $arguments);
        $this->arguments[$res['key']] = [$res['key'], $res['modelAlias'], $method, $res['param']];
        $this->allArgument[] = [$res['modelAlias'], $method, $res['param']];
        //将原本传递的参数进行缓存 保持缓存值一致性
        self::prepose($res['key'], $res['modelAlias'], $method, $res['param']);
        return $this;
    }

    /**
     * 生成缓存的key  删除请求api参数中已有缓存的参数
     *
     * @param $key
     * @param $modelAlias
     * @param $method
     * @param $param
     * @return $this
     */
    protected function prepose($key, $modelAlias, $method, $param)
    {
        $cacheKey = CacheKey::custom($modelAlias, $method, $param);
        $this->allCacheKey[$key] = $cacheKey;
        if (empty(\SConfig::get('refresh_cache')) && CacheKey::haveCache($cacheKey)) {
            unset($this->arguments[$key]);
        }
        return $this;
    }

    /**
     * 初始化
     *
     * @return $this
     */
    protected function initialize()
    {
        //初始化
        $this->arguments = [];
        $this->allCacheKey = [];
        $this->allArgument = [];
        self::reset();
        return $this;
    }
}