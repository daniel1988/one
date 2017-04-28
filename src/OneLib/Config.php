<?php

namespace OneLib;


class Config
{

    /**
     * 将当前环境转换为字符串
     */
    static function env_str()
    {
        switch (true) {
            case PRODUCTION:
                return 'production';
            case STAGING:
                return 'staging';
            case TESTING:
                return 'testing';
            default:
                return 'development';
        }
    }

    /**
     * 加载配置文件数据
     *
     *     load('database')
     *     load('database.default.adapter')
     *
     * @param  string  $name
     * @return mixed
     */
    static function load($name, $default = null)
    {
        static $cached = [];

        // 移除多余的分隔符
        $name = trim($name, '.');

        if (isset($cached[$name])) {
            return null === $cached[$name] ? $default : $cached[$name];
        }

        // 获取配置名及路径
        if (strpos($name, '.') === false) {
            $paths    = [];
            $filename = $name;
        } else {
            $paths    = explode('.', $name);
            $filename = array_shift($paths);
        }

        if (isset($cached[$filename])) {
            $data = $cached[$filename];
        } else {
            // 默认优先查找 php 数组类型的配置
            // 查找不到时，根据支持的配置类型进行查找 (注意类型的优先顺序)
            $drivers = [
                'yaml' => '\Phalcon\Config\Adapter\Yaml',
                'json' => '\Phalcon\Config\Adapter\Json',
                'ini'  => '\Phalcon\Config\Adapter\Ini',
            ];

            // 当前配置环境路径
            $path = APP_PATH . '/config/' . self::env_str();

            $file = "$path/$filename.php";
            if (is_file($file)) {
                $data = include $file;
            } else {
                // 查找配置文件
                $data = null;
                foreach ($drivers as $ext => $class) {
                    $file = "$path/$filename.$ext";
                    if (is_file($file)) {
                        $data = new $class($file);
                        break;
                    }
                }
            }

            if (is_array($data)) {
                $data = new \Phalcon\Config($data);
            }

            // 缓存文件数据
            $cached[$filename] = $data;
        }

        // 支持路径方式获取配置，例如：config('file.key.subkey')
        foreach ($paths as $key) {
            $data = isset($data->{$key}) ? $data->{$key} : null;
        }

        // 缓存数据
        $cached[$name] = $data;

        return null === $cached[$name] ? $default : $cached[$name];
    }
}