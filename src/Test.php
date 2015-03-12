<?php


namespace Ilex;


/**
 * Class Test
 * @package Ilex
 */
class Test
{
    /** @var \Ilex\Base\Model\sys\Input */
    private static $Input;

    public static function boot($APPPATH, $RUNTIMEPATH)
    {
        define('ENVIRONMENT', 'TEST');
        Autoloader::initialize($APPPATH, $RUNTIMEPATH);
        static::$Input = Core\Loader::model('sys/Input');
    }

    public static function run($url = '/', $method = 'GET', $post = array())
    {
        static::$Input->clear()->merge('post', $post);
        $_SERVER['REQUEST_URI'] =  ENV_HOST . '/' . $url;
        ob_start();
        Autoloader::resolve($method, $url);
        return ob_get_clean();
    }
}