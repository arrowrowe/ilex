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

    public static function run($APPPATH)
    {
        $ILEXPATH = self::getRealPath(__DIR__);
        require($ILEXPATH . 'core/loader.php');
        Loader::init($ILEXPATH, $APPPATH);
        require($ILEXPATH . 'route/route.php');
        $Route = new Route\Route();
        require($APPPATH . 'config/route.php');
        $Route->resolve($_SERVER['REQUEST_URI']);
    }

}