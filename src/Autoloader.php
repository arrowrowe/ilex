<?php

namespace Ilex;


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
        require_once($ILEXPATH . 'core/Loader.php');
        Loader::init($ILEXPATH, $APPPATH, $RUNTIMEPATH);

        // Include the constant file.
        include_once($APPPATH . 'config/const.php');

        // Initialize the route.
        require_once($ILEXPATH . 'route/Route.php');
    }

    public static function route()
    {
        $Route = new Route\Route();
        include(Loader::APPPATH() . 'config/route.php');
        return $Route;
    }

    public static function run($APPPATH, $RUNTIMEPATH)
    {
        static::initialize($APPPATH, $RUNTIMEPATH);
        return static::route()->resolve(
            isset($_GET['_url']) ? $_GET['_url'] : '/',
            $_SERVER['REQUEST_METHOD']
        );
    }

}