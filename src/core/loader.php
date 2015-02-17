<?php

namespace Ilex;


class Loader
{
    private static $container = array();
    private static function has($k)     { return isset(self::$container[$k]); }
    private static function get($k)     { return self::$container[$k];        }
    private static function let($k, $v) { return self::$container[$k] = $v;   }

    public static function init($ILEXPATH, $APPPATH)
    {
        self::let('ILEXPATH', $ILEXPATH);
        self::let('APPPATH', $APPPATH);
    }

    public static function db()
    {
        if (self::has('db')) {
            return self::get('db');
        } else {
            $mongo = new \MongoClient('127.0.0.1:27017', array(
                    'username'          => 'admin',
                    'password'          => 'admin',
                    'db'                => 'test',
                    'connectTimeoutMS'  => 2000
                ));
            return self::let('db', $mongo->selectDB('test'));
        }
    }

    public static function error($code, $message = 'Oops!', $title = NULL)
    {
        self::view('base/error', array(
                'title' => is_null($title) ? $code : $title,
                'message' => $message
            ));
        http_response_code($code);
        exit();
    }

    public static function controller($handler)
    {
        require_once(self::get('ILEXPATH') . 'base/controller/Base.php');
        require(self::get('APPPATH') . 'controller/' . $handler . '.php');
        $className = $handler . 'Controller';
        return new $className();
    }

    public static function view($path, $vars = NULL)
    {
        is_null($vars) || extract($vars);
        include(self::get('APPPATH') . 'view/' . $path . '.php');
    }

    public static function views($path, $option = NULL, $vars = NULL)
    {
        $option = array_merge(array(
                'title' => 'ilex'
            ), is_null($option) ? array() : $option);
        self::view('general/header', array('title' => $option['title']));
        self::view('general/nav');
        self::view($path, $vars);
        self::view('general/footer');
    }

}