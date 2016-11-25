<?php

namespace SuperView\Utils;

use GuzzleHttp\Client as HttpClient;

class Api
{
    private $http;

    private static $instance;

    private function __construct()
    {
        $this->http = new HttpClient(['base_uri'=>\Config::get('api_base_url')]);
    }

    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($params)
    {
        foreach ($params as $key => $value) {
            if (empty($value)) {
                unset($params[$key]);
            }
        }
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
