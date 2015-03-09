<?php

namespace Ilex;


use Ilex\Core\Loader;

/**
 * Class Autoloader
 * @package Ilex
 */
class Autoloader
{
    public static function getRealPath($path)
    {
        if (($_temp = realpath($path)) !== FALSE) {
            $path = $_temp . '/';
        } else {
            $path = rtrim($path, '/') . '/';
        }
        return $path;
    }

    public static function initialize($APPPATH, $RUNTIMEPATH)
    {
        $ILEXPATH = self::getRealPath(__DIR__);
        $APPPATH = self::getRealPath($APPPATH);
        $RUNTIMEPATH = self::getRealPath($RUNTIMEPATH);

        // Initialize the loader.
        Loader::init($ILEXPATH, $APPPATH, $RUNTIMEPATH);

        // Include the constant file.
        include_once($APPPATH . 'config/const.php');

        // todo: Define necessary constants if not defined...
    }

    public static function resolve($method, $url)
    {
        $Route = new Route\Route($method, $url);
        include(Loader::APPPATH() . 'config/route.php');
        return $Route->result();
    }

    public static function run($APPPATH, $RUNTIMEPATH)
    {
        static::initialize($APPPATH, $RUNTIMEPATH);
        static::resolve(
            $_SERVER['REQUEST_METHOD'],
            isset($_GET['_url']) ? $_GET['_url'] : '/'
        );
    }

}