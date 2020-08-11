<?php

if (!function_exists('get_server_url')) {
    /*
     * 获取服务器网址
     */
    function get_server_url($full = true)
    {
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $http_url = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        if ($full) {
            return $http_type . $http_url;
        } else {
            return $http_url;
        }
    }
}

if (!function_exists('url_pluck')) {
    /*
     * 提取网址去掉http://|https:// 和 ?之后 部分
     */
    function url_pluck($url)
    {
        $domain = $url;
        if (preg_match('/(http:\/\/)|(https:\/\/)/i', $url)) {
            $domain = preg_replace('/(http:\/\/)|(https:\/\/)/i', '', $url);
        }
        if (strstr($domain, '?')) {
            $domain = substr($domain, 0, strrpos($domain, "?"));
        }
        if (strstr($domain, '/')) {
            $domain = substr($domain, 0, strrpos($domain, "/"));
        }
        return $domain;
    }
}

if (!function_exists('short_url')) {
    /**
     * 短链接生成
     *
     * @param $url
     * @return string
     */
    function short_url($url)
    {
        $url = crc32($url);
        $result = sprintf("%u", $url);
        return code62($result);
    }
}

if (!function_exists('code62')) {
    function code62($x)
    {
        $show = '';
        while ($x > 0) {
            $s = $x % 62;
            if ($s > 35) {
                $s = chr($s + 61);
            } elseif ($s > 9 && $s <= 35) {
                $s = chr($s + 55);
            }
            $show .= $s;
            $x = floor($x / 62);
        }
        return $show;
    }
}

if (!function_exists('php_run_path')) {
    /*
     * 获取php运行路径
     */
    function php_run_path()
    {
        if (substr(strtolower(PHP_OS), 0, 3) == 'win') {
            $ini = ini_get_all();
            $path = $ini['extension_dir']['local_value'];
            $php_path = str_replace('\\', '/', $path);
            $php_path = str_replace(array('/ext/', '/ext'), array('/', '/'), $php_path);
            $real_path = $php_path . 'php.exe';
        } else {
            $real_path = PHP_BINDIR . '/php';
        }
        if (strpos($real_path, 'ephp.exe') !== FALSE) {
            $real_path = str_replace('ephp.exe', 'php.exe', $real_path);
        }
        return isset($real_path) ? $real_path : 'php';
    }
}

if (!function_exists('get_base_url')) {
    /**
     * 获取基础Url
     *
     * @return string
     */
    function get_base_url()
    {
        $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https://" : "http://";
        $base_url .= $_SERVER["SERVER_NAME"];
        $base_url .= ($_SERVER["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER["SERVER_PORT"]);

        return $base_url;
    }
}
if (!function_exists('array_to_tree')) {
    /**
     * 数组转树状
     *
     * @param $data // 原始数据（二维数组）
     * @param $parent_key // 原始数据父级字段
     * @param $child_key // 生成子集的字段
     * @return array
     */
    function array_to_tree($data, $parent_key, $child_key)
    {
        $tree = [];
        foreach ($data as $key => $val) {
            if ($val[$parent_key] == 0) {
                $tree[] = &$data[$key];
            } else {
                $data[$val[$parent_key]][$child_key][] = &$data[$key];
            }
        }
        return $tree;
    }
}

if (!function_exists('file_size_format')) {
    /**
     * 文件大小格式化
     *
     * @param $size
     * @return string
     */
    function file_size_format($size = 0)
    {
        if (empty($size)) {
            return 0;
        }

        if (!is_numeric($size)) {
            return 0;
        }

        if ($size < 1024) {
            return 0;
        }

        $unit = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $size >= 1024 && $i <= 4; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . $unit[$i];
    }
}

if (!function_exists('generate_china_char')) {
    /**
     * 生成中文字符串
     *
     * @param $num
     * @return string
     */
    function generate_china_char($num)
    {
        $b = '';
        for ($i = 0; $i < $num; $i++) {
            // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
            $a = chr(mt_rand(0xB0, 0xD0)) . chr(mt_rand(0xA1, 0xF0));
            // 转码
            $b .= iconv('GB2312', 'UTF-8', $a);
        }
        return $b;
    }
}

if (!function_exists('date_diff_format')) {
    /**
     * 计算两个日期相差
     *
     * @param $start_date
     * @param $end_date
     * @param $date_unit
     * @return false|float
     */
    function date_diff_format($start_date, $end_date = null, $date_unit = null)
    {
        if (empty($end_date)) {
            $end_date = date("y-m-d H:i:s");
        }

        $year = floor((strtotime($end_date) - strtotime($start_date)) / 86400 / 365); // 年
        $day = floor((strtotime($end_date) - strtotime($start_date)) / 86400); // 天
        $hour = floor((strtotime($end_date) - strtotime($start_date)) % 86400 / 3600); // 小时
        $minute = floor((strtotime($end_date) - strtotime($start_date)) % 86400 / 60); // 分钟
        $second = floor((strtotime($end_date) - strtotime($start_date)) % 86400 % 60); // 秒

        $data = null;
        switch ($date_unit) {
            case "y":
                $data = $year;
                break;
            case "d":
                $data = $day;
                break;
            case "h":
                $data = $hour;
                break;
            case "m":
                $data = $minute;
                break;
            case "s":
                $data = $second;
                break;
            default:
                $data = "$day";
                break;
        }
        return $data;
    }
}

?>
