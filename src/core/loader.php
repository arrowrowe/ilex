<?php

namespace Ilex\Core;


/**
 * Class Loader
 * @package Ilex\Core
 */
class Loader
{
    private static $container = array();
    private static function has($k)            { return isset(self::$container[$k]);    }
    private static function get($k)            { return self::$container[$k];           }
    private static function let($k, $v)        { return self::$container[$k]      = $v; }
    private static function letTo($k, $kk, $v) { return self::$container[$k][$kk] = $v; }

    public static function init($ILEXPATH, $APPPATH, $RUNTIMEPATH)
    {
        self::let('ILEXPATH', $ILEXPATH);
        self::let('APPPATH', $APPPATH);
        self::let('RUNTIMEPATH', $RUNTIMEPATH);

        self::let('Controller', array());
        self::let('Model', array());
    }

    public static function APPPATH()     { return self::get('APPPATH');     }
    public static function RUNTIMEPATH() { return self::get('RUNTIMEPATH'); }

    public static function db()
    {
        if (self::has('db')) {
            return self::get('db');
        } else {
            $mongo = new \MongoClient(SVR_MONGO_HOST . ':' . SVR_MONGO_PORT, array(
                'username'          => SVR_MONGO_USER,
                'password'          => SVR_MONGO_PASS,
                'db'                => SVR_MONGO_DB,
                'connectTimeoutMS'  => SVR_MONGO_TIMEOUT
            ));
            return self::let('db', $mongo->selectDB(SVR_MONGO_DB));
        }
    }

    public static function controller($path) { return self::loadWithBase($path, 'Controller'); }
    public static function      model($path) { return self::loadWithBase($path,      'Model'); }

    public static function isModelLoaded($path) { return self::isLoadedWithBase($path, 'Model'); }

    private static function isLoadedWithBase($path, $type)
    {
        $typeEntities = self::get($type);
        return isset($typeEntities[$path]);
    }

    private static function loadWithBase($path, $type)
    {
        // Ensure that for each model only one entity is loaded.
        $typeEntities = self::get($type);
        if (isset($typeEntities[$path])) {
            return $typeEntities[$path];
        } else {
            $className = static::load($path, $type);
            if ($className === FALSE) {
                throw new \Exception(ucfirst($type) . ' ' . $path . ' not found.');
            }
            $class = new $className;
            return self::letTo($type, $path, $class);
        }
    }

    private static function load($path, $type)
    {
        foreach (array(
            'app' => array(
                'path' => self::get('APPPATH') . $type . '/' . $path . $type . '.php',
                'name' => self::getHandlerFromPath($path) . $type
            ),
            'ilex' => array(
                'path' => self::get('ILEXPATH') . 'Base/' . $type . '/' . $path . '.php',
                'name' => '\\Ilex\\Base\\Model\\' . str_replace('/', '\\', $path)
            )
        ) as $item) {
            if (file_exists($item['path'])) {
                include($item['path']);
                return $item['name'];
            }
        }
        return FALSE;
    }

    public static function getHandlerFromPath($path)
    {
        $handler = strrchr($path, '/');
        return $handler === FALSE ? $path : substr($handler, 1);
    }

}