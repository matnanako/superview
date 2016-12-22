<?php

namespace SuperView\Utils;

use GuzzleHttp\Client as HttpClient;

class Api
{
    private $http;

    private static $instance;

    private function __construct()
    {
        $apiUrl = \SConfig::get('api_base_url');
        if (empty($apiUrl)) {
            throw new \Exception("请设置SuperView配置`api_base_url`!");

        }
        $this->http = new HttpClient(['base_uri' => $apiUrl]);
    }

    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get data from web service.
     *
     * @param  array  $params
     * @return array
     */
    public function get($params)
    {
        // 过滤空的参数
        $params = array_filter($params, function ($value) {
            return !empty($value);
        });
        // 生成get查询
        $params = ['query'=>$params];
        $data = $this->getData($params);
        return json_decode($data, true);
    }

    private function getData($params, $cache = true)
    {
        $response = $this->http->get('', $params);
        $body = $response->getBody();
        $data = $body->getContents();
        return $data;
    }
}
