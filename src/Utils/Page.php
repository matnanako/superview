<?php
namespace SuperView\Utils;

class Page
{
    // 是否简洁模式
    protected $simple;
    // 链接url路由格式
    protected $route;
    // 每页数据量
    protected $perPage;
    // 当前分页
    protected $currentPage;
    // 总分页数
    protected $totalPage;
    // 分页配置参数
    protected $options;

    /**
     * Create a new paginator instance.
     *
     * @param  string  $route
     * @param  int  $total
     * @param  int  $perPage
     * @param  int|null  $currentPage
     * @param  boolean  $simple
     * @param  array  $options
     * @return void
     */
    public function __construct($route, $total, $perPage, $currentPage = null, $simple = false, $options = [])
    {
        $this->route = $route;
        $this->simple = $simple;
        $this->perPage = intval($perPage);
        $this->totalPage = $perPage >= 1 ? ceil(intval($total) / $this->perPage) : 1;
        $this->hasMore = $this->totalPage > 1;
        $this->setCurrentPage($currentPage);

        $this->options = \SConfig::get('pagination');
        $this->options = is_array($options) ? array_merge($this->options, $options) : $this->options;
    }

    protected function setCurrentPage($currentPage)
    {
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
    protected function url($page)
    {
        if ($page <= 0) {
            $page = 1;
        }

        return str_replace('{page}', $page, $this->route);
    }

    /**
     * 创建一组分页链接
     *
     * @param  int $start
     * @param  int $end
     * @return array
     */
    protected function getUrlRange($start, $end)
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
        return str_replace('{total}', $this->totalPage, $this->options['total']);
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

        return str_replace('{url}', $url, $this->options['previous']);
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

        return str_replace('{url}', $url, $this->options['next']);
    }

    /**
     * 页码按钮
     * @return string
     */
    protected function getLinks()
    {
        if ($this->simple) {
            return '';
        }
        $block = [
            'first'  => null,
            'slider' => null,
            'last'   => null
        ];

        $side = 2;
        $both = $side * 2; // 表示当前页两侧显示多少个页面

        // 1...,4,5,6,7,8 ... N, 其中...应该表示至少两个页面
        if ($this->totalPage <= $both + 5) { // 1,2,3,4,5,6,7,总分页如果小于等于both+当前页1+(...)2*2则显示全部分页
            $block['first'] = $this->getUrlRange(1, $this->totalPage);
        } elseif ($this->currentPage <= $both) { // 1,2,3,4,5...8, 当前分页在both范围内显示此种效果
            $block['first'] = $this->getUrlRange(1, $both + 1);
            $block['last']  = $this->getUrlRange($this->totalPage, $this->totalPage);
        } elseif ($this->currentPage > ($this->totalPage - $both)) { // 1...4,5,6,7,8
            $block['first'] = $this->getUrlRange(1, 1);
            $block['last']  = $this->getUrlRange($this->totalPage - ($both + 1), $this->totalPage);
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
                $this->options['layout']
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
        return str_replace(['{url}', '{page}'], [$url, $page], $this->options['links']);
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
        return str_replace('{page}', $page, $this->options['link_active']);
    }

    /**
     * 生成省略号按钮
     *
     * @return string
     */
    protected function getDots()
    {
        return $this->options['dots'];
    }
}
