<?php


namespace Ilex;

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../src/Autoloader.php');


Test::boot();


/**
 * Class Test
 * @package Ilex
 */
class Test
{
    /** @var Route\Route */
    private static $Route;

    public static function boot()
    {
        Autoloader::initialize(__DIR__ . '/app', __DIR__ . '/runtime');
        static::$Route = Autoloader::route();
    }

    public static function run($url = '/', $method = 'GET')
    {
        ob_start();
        static::$Route->resolve($url, $method);
        return ob_get_clean();
    }
}