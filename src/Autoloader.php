<?php

namespace Ilex;


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

    public static function run($APPPATH, $RUNTIMEPATH)
    {
        $ILEXPATH = self::getRealPath(__DIR__);
        $APPPATH = self::getRealPath($APPPATH);
        $RUNTIMEPATH = self::getRealPath($RUNTIMEPATH);

        // Initialize the loader.
        require_once($ILEXPATH . 'core/loader.php');
        Loader::init($ILEXPATH, $APPPATH, $RUNTIMEPATH);

        // Initialize the route.
        require_once($ILEXPATH . 'route/route.php');
        $Route = new Route\Route();

        // Configure the route.
        require($APPPATH . 'config/route.php');

        // Resolve.
        $Route->resolve($_SERVER['REQUEST_URI']);
    }

}