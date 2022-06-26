<?php
namespace app\common\controller;

class Safe
{
    private $config = array();
    /**
     * WebScan constructor.
     * @param array $config
     */
    public function __construct($config = array())
    {
        //默认配置
        $config = array(
            'url' => $_SERVER['REQUEST_URI'], //验证的url
            'switch' => 1,
            'white_urls' => array(
                '^/admin/*', //路径白名单 (^)匹配开头(*)匹配任意字符 不含括号
                '^/ajax_format.html=>post.code,get.code,cookie.code', //路径参数白名单 当匹配到指定地址时 不验证指定的参数
            ),
            'http_html' => base64_decode('PCFET0NUWVBFIEhUTUw+DQo8aHRtbD4NCjxoZWFkPg0KPG1ldGEgaHR0cC1lcXVpdj0iQ29udGVudC1UeXBlIiBjb250ZW50PSJ0ZXh0L2h0bWw7IGNoYXJzZXQ9dXRmLTgiIC8+DQo8dGl0bGU+572R56uZ6Ziy54Gr5aKZPC90aXRsZT4NCjxzdHlsZT4NCip7cGFkZGluZzowO21hcmdpbjowfQ0KYm9keXtmb250OjE0cHgvMS41IE1pY3Jvc29mdCBZYWhlaSzlrovkvZMsc2Fucy1zZXJpZjtjb2xvcjojNTU1O2xpbmUtaGVpZ2h0OjI1cHh9DQo8L3N0eWxlPg0KPC9oZWFkPg0KPGJvZHk+DQo8ZGl2IHN0eWxlPSJ3aWR0aDo2MDBweDtjbGVhcjpib3RoO21hcmdpbjowIGF1dG87bWFyZ2luLXRvcDoxMCUiPg0KICA8ZGl2IHN0eWxlPSJsaW5lLWhlaWdodDo0MHB4O2NvbG9yOiNmZmY7Zm9udC1zaXplOjE2cHg7YmFja2dyb3VuZDojNmJiM2Y2O3BhZGRpbmctbGVmdDoyMHB4OyI+572R56uZ6Ziy54Gr5aKZPC9kaXY+DQogIDxkaXYgc3R5bGU9ImJvcmRlcjoxcHggZGFzaGVkICNjZGNlY2U7Ym9yZGVyLXRvcDpub25lO2ZvbnQtc2l6ZToxNHB4O2hlaWdodDoyMjBweDtwYWRkaW5nOjIwcHg7YmFja2dyb3VuZDojZjNmN2Y5OyI+DQogICAgPHAgc3R5bGU9ImZvbnQtd2VpZ2h0OjYwMDtjb2xvcjojZmM0ZjAzOyI+5oKo55qE6K+35rGC5bim5pyJ5LiN5ZCI5rOV5Y+C5pWw77yM5bey6KKr572R56uZ566h55CG5ZGY6K6+572u5oum5oiq77yBPC9wPg0KICAgIDxwPuWPr+iDveWOn+WboO+8muaCqOaPkOS6pOeahOWGheWuueWMheWQq+WNsemZqeeahOaUu+WHu+ivt+axgjwvcD4NCiAgICA8cCBzdHlsZT0ibWFyZ2luLXRvcDoxNXB4OyI+5aaC5L2V6Kej5Yaz77yaPC9wPg0KICAgIDxwPjHvvInmo4Dmn6Xmj5DkuqTlhoXlrrnvvJs8L3A+DQogICAgPHA+Mu+8ieWmgue9keermeaJmOeuoe+8jOivt+iBlOezu+epuumXtOaPkOS+m+WVhu+8mzwvcD4NCiAgICA8cD4z77yJ5pmu6YCa572R56uZ6K6/5a6i77yM6K+36IGU57O7572R56uZ566h55CG5ZGY77ybPC9wPg0KICA8L2Rpdj4NCjwvZGl2Pg0KPC9ib2R5Pg0KPC9odG1sPg=='),
            'rules' => array(
                'get' => array(
                    'switch' => 1,
                    'content' => $_GET,
                    'filter' => "\\<.+javascript:window\\[.{1}\\\\x|<.*=(&#\\d+?;?)+?>|<.*(data|src)=data:text\\/html.*>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\(|benchmark\s*?\(.*\)|sleep\s*?\(.*\)|\\b(group_)?concat[\\s\\/\\*]*?\\([^\\)]+?\\)|\bcase[\s\/\*]*?when[\s\/\*]*?\([^\)]+?\)|load_file\s*?\\()|<[a-z]+?\\b[^>]*?\\bon([a-z]{4,})\s*?=|^\\+\\/v(8|9)|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)|UPDATE\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)@{0,2}(\\(.+\\)|\\s+?.+?\\s+?|(`|'|\").*?(`|'|\"))FROM(\\(.+\\)|\\s+?.+?|(`|'|\").*?(`|'|\"))|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)",
                ),
                'post' => array(
                    'switch' => 1,
                    'content' => $_POST,
                    'filter' => "<.*=(&#\\d+?;?)+?>|<.*data=data:text\\/html.*>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\(|benchmark\s*?\(.*\)|sleep\s*?\(.*\)|\\b(group_)?concat[\\s\\/\\*]*?\\([^\\)]+?\\)|\bcase[\s\/\*]*?when[\s\/\*]*?\([^\)]+?\)|load_file\s*?\\()|<[^>]*?\\b(onerror|onmousemove|onload|onclick|onmouseover)\\b|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)|UPDATE\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)(\\(.+\\)|\\s+?.+?\\s+?|(`|'|\").*?(`|'|\"))FROM(\\(.+\\)|\\s+?.+?|(`|'|\").*?(`|'|\"))|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)",
                ),
                'cookie' => array(
                    'switch' => 1,
                    'content' => $_COOKIE,
                    'filter' => "benchmark\s*?\(.*\)|sleep\s*?\(.*\)|load_file\s*?\\(|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)|UPDATE\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)@{0,2}(\\(.+\\)|\\s+?.+?\\s+?|(`|'|\").*?(`|'|\"))FROM(\\(.+\\)|\\s+?.+?|(`|'|\").*?(`|'|\"))|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)",
                ),
                'referer' => array(
                    'switch' => 1,
                    'content' => $_SERVER['HTTP_REFERER'],
                    'filter' => "\\<.+javascript:window\\[.{1}\\\\x|<.*=(&#\\d+?;?)+?>|<.*(data|src)=data:text\\/html.*>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\(|benchmark\s*?\(.*\)|sleep\s*?\(.*\)|\\b(group_)?concat[\\s\\/\\*]*?\\([^\\)]+?\\)|\bcase[\s\/\*]*?when[\s\/\*]*?\([^\)]+?\)|load_file\s*?\\()|<[a-z]+?\\b[^>]*?\\bon([a-z]{4,})\s*?=|^\\+\\/v(8|9)|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)|UPDATE\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)@{0,2}(\\(.+\\)|\\s+?.+?\\s+?|(`|'|\").*?(`|'|\"))FROM(\\(.+\\)|\\s+?.+?|(`|'|\").*?(`|'|\"))|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)",
                ),
            ),
        );
        //格式化白名单
        if (!is_array($config['white_urls']) && $config['white_urls']) {
            $config['white_urls'] = explode("\n", $config['white_urls']);
        }
        //遍历规则排除白名单参数 并删除 该白名单网址
        foreach ($config['white_urls'] as $index => $url) {
            $url = trim($url);
            if (strpos($url, '=>')) {
                //获取白名单参数
                list($_url, $_param) = explode('=>', $url);
                //判断是否符合当前url
                $_url = preg_quote($_url, '/');
                $_url = str_replace('\^', '^', $_url);
                $_url = str_replace('\*', '[\s\S]*', $_url);
                if (preg_match("/" . $_url . "/is", $config['url'], $matches)) {
                    $params = explode(',', $_param);
                    foreach ($params as $param) {
                        list($type, $name) = explode('.', $param);
                        unset($config['rules'][$type]['content'][$name]);
                    }
                }
                unset($config['white_urls'][$index]);
            }
        }
        $this->config = $config;
    }
    /**
     * 判断是否在白名单里
     */
    public function isInWhiteList()
    {
        if (!$this->config['white_urls']) {
            return false;
        }
        //判断白名单(^)匹配开头(*)匹配任意字符 不含括号
        foreach ($this->config['white_urls'] as $url) {
            $url = trim($url);
            $url = preg_quote($url, '/');
            $url = str_replace('\^', '^', $url);
            $url = str_replace('\*', '[\s\S]*', $url);
            if (preg_match("/" . $url . "/is", $this->config['url'], $matches)) {
                return true;
            }
        }
        return false;
    }
    /**
     * 检测
     * @return bool
     */
    public function isAttack()
    {
        //判断防护开关
        if (!$this->config['switch']) {
            return false;
        }
        //判断白名单
        if ($this->isInWhiteList()) {
            return false;
        }
        //遍历判断规则
        foreach ($this->config['rules'] as $rule) {
            if ($rule['switch'] && $rule['content'] && $rule['filter']) {
                $content = var_export($rule['content'], true);
                if (preg_match("/" . $rule['filter'] . "/is", $content, $matches)) {
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * 显示禁止页面
     */
    public function showStopPage()
    {
        echo $this->config['http_html'];
        exit();
    }
}