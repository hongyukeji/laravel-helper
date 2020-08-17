<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (!function_exists('route_url')) {
    /**
     * 自动获取route或url
     *
     * @param $name
     * @param array $parameters
     * @param bool $absolute
     * @return mixed
     */
    function route_url($name, $parameters = [], $absolute = true)
    {
        if (Route::has($name)) {
            return route($name, $parameters, $absolute);
        } else {
            $url = $name;
            if (Str::startsWith($name, ['/'])) {
                $app_url = config('app.url');
                // app()->environment() // 生产环境返回true
                if (strcmp($app_url, "http://localhost") == 0) {
                    $app_url = get_server_url();
                }
                $url_prefix = Str::endsWith($app_url, '/') ? Str::beforeLast($app_url, '/') : $app_url;
                $url = !empty(Str::after($name, '/')) ? $url_prefix . $name : $url_prefix;
            }
            if (!empty($parameters) && is_array($parameters)) {
                $url = Str::finish($url, '?') . http_build_query($parameters);
            } else if (!empty($parameters)) {
                $url = Str::finish($url, '/') . $parameters;
            }
            return $url;
        }
    }
}

if (!function_exists('route_name')) {
    /**
     * 根据网址获取路由名称
     *
     * @param $url // url('/')
     * @return string|null
     */
    function route_name($url)
    {
        return app('router')->getRoutes()->match(app('request')->create($url))->getName();
    }
}

if (!function_exists('route_has')) {
    /**
     * 判断路由名称是否存在
     *
     * @param $route
     * @return mixed
     */
    function route_has($route)
    {
        return Route::has($route);
    }
}

if (!function_exists('set_env')) {
    /**
     * 修改系统 .env 文件
     *
     * @param array $data
     * @return bool
     */
    function set_env(array $data)
    {
        $envPath = base_path('.env');
        $contentCollect = collect(file($envPath, FILE_IGNORE_NEW_LINES));
        $contentCollect->transform(function ($item) use (&$data) {
            foreach ($data as $key => $value) {
                if (Str::startsWith($item, $key)) {
                    $env = $key . '=' . $value;
                    unset($data[$key]);
                    return $env;
                }
            }
            return $item;
        });
        $contentArray = $contentCollect->toArray();
        foreach ($data as $k => $v) {
            array_push($contentArray, $k . '=' . $v);
        }
        //$content = implode($contentArray, "\n");
        if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
            $content = implode("\n", $contentArray);
        } else {
            $content = implode($contentArray, "\n");
        }
        file_put_contents($envPath, $content);
        return true;
    }
}

if (!function_exists('assets')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function assets($path, $secure = null)
    {
        if (Str::endsWith($path, '.css') || Str::endsWith($path, '.js')) {
            return app('url')->asset($path . '?v=' . config('app.version'), $secure);
        } else {
            return app('url')->asset($path, $secure);
        }
    }
}

if (!function_exists('asset_url')) {
    /**
     * 获取系统存储盘对应路径 url
     *
     * @param $path
     * @param array $options
     * @return mixed
     */
    function asset_url($path, $options = [])
    {
        if (empty($path)) {
            return null;
        }
        switch ($path) {
            case Str::startsWith($path, ['/']):
                return get_server_url() . $path;
                break;
            case Str::startsWith($path, ['http://', 'https://', 'ftp://', 'data:image/']):
                return $path;
                break;
            default:
                return Str::finish(get_server_url(), '/') . $path;
        }
    }
}

if (!function_exists('str_uuid')) {
    /**
     * 生成 uuid
     *
     * @return string
     */
    function str_uuid()
    {
        return (string)Str::uuid();
    }
}

if (!function_exists('storage_disk_url')) {
    /**
     * 获取存储硬盘url
     *
     * @param null $disk
     * @return string
     */
    function storage_disk_url($disk = null)
    {
        if (empty($disk)) {
            $disk = config('filesystems.default');
        }
        return $storage_disk_url = config("filesystems.disks.{$disk}.url");
    }
}
