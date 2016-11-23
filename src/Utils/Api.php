<?php

namespace SuperView\Utils;

use GuzzleHttp\Client as HttpClient;

class Api
{
    protected $http;

    public function __construct()
    {
        $this->http = new HttpClient(['base_uri'=>\Config::get('api_base_url')]);
    }

    public function get($params)
    {
        $this->convertToApiParams($params);
        $params = ['query'=>$params];
        $data = $this->getData($params);
        return json_decode($data, true);
    }

    public function post($params)
    {
        $this->convertToApiParams($params);
        $params = ['form_params'=>$params];
        $data = $this->getData($params, false);
        return json_decode($data, true);
    }

    private function getData($params, $cache = true)
    {
        $response = $this->http->post('', $params);
        $body = $response->getBody();
        $data = $body->getContents();
        return $data;
    }

    private function convertToApiParams(&$params)
    {
        if (!empty($params['category'])) {
            $params['c'] = substr($params['category'], 0, strpos($params['category'], "/"));
            $params['classid'] = \Config::get('categories')[$params['category']];
        }
    }
}
