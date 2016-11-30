<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: zhangyajun <448901948@qq.com>
// +----------------------------------------------------------------------

namespace SuperView\Utils;

class Page
{
    protected $configs;

    protected $perPage;

    protected $currentPage;

    protected $totalPage;

    protected $path = '/';

    protected $query = [];

    protected $fragment = null;

    protected $pageName = 'p';

    protected $options = [];

    /**
     * Create a new paginator instance.
     *
     * @param  mixed  $items
     * @param  int  $perPage
     * @param  array  $options (path, query, fragment, pageName)
     * @return void
     */
    public function __construct($total, $perPage, $options)
    {
        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }

        $urlInfo = parse_url($_SERVER['REQUEST_URI']);

        if (!isset($options['path'])) {
            $this->path = $urlInfo['path'];
        } else {
            $this->path = $this->path != '/' ? rtrim($this->path, '/') : $this->path;
        }

        if (isset($urlInfo['query'])) {
            $query = parse_str($urlInfo['query'], $queries);

            if (count($queries) > 0) {
                $this->query = array_merge($this->query, $queries);
            }
        }

        $this->perPage = $perPage;
        $this->totalPage = $perPage > 1 ? ceil($total / $perPage) : 1;
        $this->hasMore = $this->totalPage > 1;
        $this->setCurrentPage();

        $this->configs = \SConfig::get('pagination');
    }

    protected function setCurrentPage()
    {
        $currentPage = empty($_GET[$this->pageName]) ? 0 : intval($_GET[$this->pageName]);
        $this->currentPage = (filter_var($currentPage, FILTER_VALIDATE_INT) !== false 
            && (int) $currentPage >= 1
            && (int) $currentPage <= $this->totalPage) ? $currentPage : 1;
    }

    /**
     * 获取页码对应的链接
     *
     * @param $page
     * @return string
     */
    public function url($page)
    {
        if ($page <= 0) {
            $page = 1;
        }

        $parameters = [$this->pageName => $page];

        if (count($this->query) > 0) {
            $parameters = array_merge($this->query, $parameters);
        }

        return $this->path
                        .(strpos($this->path, '?') !== false ? '&' : '?')
                        .http_build_query($parameters, '', '&')
                        .$this->buildFragment();
    }

    /**
     * 创建一组分页链接
     *
     * @param  int $start
     * @param  int $end
     * @return array
     */
    public function getUrlRange($start, $end)
    {
        $urls = [];

        for ($page = $start; $page <= $end; $page++) {
            $urls[$page] = $this->url($page);
        }

        return $urls;
    }

    /**
     * 构造锚点字符串
     *
     * @return string
     */
    protected function buildFragment()
    {
        return $this->fragment ? '#' . $this->fragment : '';
    }

    /**
     * 上一页按钮
     * @param string $text
     * @return string
     */
    protected function getTotalInfo()
    {
        return str_replace('{total}', $this->totalPage, $this->configs['total']);
    }

    /**
     * 上一页按钮
     * @param string $text
     * @return string
     */
    protected function getPreviousButton()
    {
        if ($this->currentPage <= 1) {
            return $this->getDisabledTextWrapper();
        }

        $url = $this->url($this->currentPage - 1);

        return str_replace('{url}', $url, $this->configs['previous']);
    }

    /**
     * 下一页按钮
     * @param string $text
     * @return string
     */
    protected function getNextButton()
    {
        if ($this->currentPage == $this->totalPage) {
            return $this->getDisabledTextWrapper();
        }

        $url = $this->url($this->currentPage + 1);

        return str_replace('{url}', $url, $this->configs['next']);
    }

    /**
     * 页码按钮
     * @return string
     */
    protected function getLinks()
    {
        $block = [
            'first'  => null,
            'slider' => null,
            'last'   => null
        ];

        $side   = 2;
        $window = $side * 2;
        
        if ($this->totalPage < $window * 2) {
            $block['first'] = $this->getUrlRange(1, $this->totalPage);
        } elseif ($this->currentPage <= $window) {
            $block['first'] = $this->getUrlRange(1, $window + 1);
            $block['last']  = $this->getUrlRange($this->totalPage - 1, $this->totalPage);
        } elseif ($this->currentPage > ($this->totalPage - $window)) {
            $block['first'] = $this->getUrlRange(1, 1);
            $block['last']  = $this->getUrlRange($this->totalPage - ($window + 1), $this->totalPage);
        } else {
            $block['first']  = $this->getUrlRange(1, 1);
            $block['slider'] = $this->getUrlRange($this->currentPage - $side, $this->currentPage + $side);
            $block['last']   = $this->getUrlRange($this->totalPage, $this->totalPage);
        }

        $html = '';

        if (is_array($block['first'])) {
            $html .= $this->getUrlLinks($block['first']);
        }

        if (is_array($block['slider'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['slider']);
        }

        if (is_array($block['last'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['last']);
        }

        return $html;
    }


    /**
     * 渲染分页html
     * @return mixed
     */
    public function render()
    {
        if ($this->hasMore) {
            return str_replace(
                ['{total}', '{previous}', '{links}', '{next}'],
                [$this->getTotalInfo(), $this->getPreviousButton(), $this->getLinks(), $this->getNextButton()],
                $this->configs['layout']
            );
        } else {
            return '';
        }
    }

    /**
     * 批量生成页码按钮.
     *
     * @param  array $urls
     * @return string
     */
    protected function getUrlLinks(array $urls)
    {
        $html = '';

        foreach ($urls as $page => $url) {
            $html .= $this->getPageLinkWrapper($url, $page);
        }

        return $html;
    }

    /**
     * 生成普通页码按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getPageLinkWrapper($url, $page)
    {
        if ($page == $this->currentPage) {
            return $this->getActivePageWrapper($page);
        }

        return $this->getAvailablePageWrapper($url, $page);
    }

    /**
     * 生成一个可点击的按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page)
    {
        return str_replace(['{url}', '{page}'], [$url, $page], $this->configs['links']);
    }

    /**
     * 生成一个禁用的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getDisabledTextWrapper()
    {
        return '';
    }

    /**
     * 生成一个激活的按钮
     *
     * @param  string $page
     * @return string
     */
    protected function getActivePageWrapper($page)
    {
        return str_replace('{page}', $page, $this->configs['link_active']);
    }

    /**
     * 生成省略号按钮
     *
     * @return string
     */
    protected function getDots()
    {
        return $this->configs['dots'];
    }
}