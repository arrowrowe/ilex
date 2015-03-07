<?php


namespace Ilex;


/**
 * Class Test
 * @package Ilex
 */
class Test
{
    /** @var \Ilex\Route\Route */
    private static $Route;
    /** @var \InputModel */
    private static $Input;

    public static function boot($APPPATH, $RUNTIMEPATH)
    {
        Autoloader::initialize($APPPATH, $RUNTIMEPATH);
        static::$Route = Autoloader::route();
        static::$Input = Loader::model('sys/Input');
    }

    public static function run($url = '/', $method = 'GET', $post = array())
    {
        static::$Input->clear()->merge('post', $post);
        ob_start();
        static::$Route->resolve($url, $method);
        return ob_get_clean();
    }
}