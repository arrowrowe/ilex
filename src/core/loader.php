<?php

namespace Ilex;


class Loader
{
    private static $container = array();
    private static function has($k)     { return isset(self::$container[$k]); }
    private static function get($k)     { return self::$container[$k];        }
    private static function let($k, $v) { return self::$container[$k] = $v;   }

    public static function init($ILEXPATH, $APPPATH, $RUNTIMEPATH)
    {
        self::let('ILEXPATH', $ILEXPATH);
        self::let('APPPATH', $APPPATH);
        self::let('RUNTIMEPATH', $RUNTIMEPATH);
    }

    public static function twig()
    {
        if (!self::has('twig')) {
            // Initialize an empty array for twig variables.
            self::let('twigVars', array());
            self::let('twig',
                new \Twig_Environment(
                    new \Twig_Loader_Filesystem(self::get('APPPATH') . 'view/'),
                    array(
                        'cache' => self::get('RUNTIMEPATH') . 'twig_compile/',
                        'auto_reload' => TRUE,
                    )
                )
            );
        }
    }

    public static function assign($vars)
    {
        self::let('twigVars', array_merge(self::get('twigVars'), $vars));
    }

    public static function render($template)
    {
        $twig = self::get('twig');
        echo($twig->render($template, self::get('twigVars')));
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

    /*
     * Show an error page.
     * @param int $code
     * @param str $message
     * @param str $title
     * @todo Use twig to render an error page.
     */
    public static function error($code, $message = 'Oops!', $title = NULL)
    {
        // @todo: Use twig to rewrite this.
        // self::view('base/error', array(
        //         'title' => is_null($title) ? $code : $title,
        //         'message' => $message
        //     ));
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

}