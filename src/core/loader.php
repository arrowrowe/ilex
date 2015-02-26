<?php

namespace Ilex;


/**
 * Class Loader
 * @package Ilex
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

        self::let('controller', array());
        self::let('model', array());
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

    /**
     * Show an error page and exit.
     * Note: app/view/base/error.twig must exist.
     * @param int $status
     * @param string $message
     * @param int $code
     * @return NULL
     */
    public static function error($status, $message = 'Oops!', $code = 1)
    {
        echo(json_encode(array('code' => $code, 'message' => $message)));
        http_response_code($status);
        exit();
    }

    public static function controller($handler) { return self::loadWithBase($handler, 'controller'); }
    public static function      model($handler) { return self::loadWithBase($handler,      'model'); }

    public static function isModelLoaded($handler) { return self::isLoadedWithBase($handler, 'model'); }

    private static function isLoadedWithBase($handler, $type)
    {
        $typeEntities = self::get($type);
        return isset($typeEntities[$handler]);
    }

    private static function loadWithBase($handler, $type)
    {
        // Ensure that for each model only one entity is loaded.
        $typeEntities = self::get($type);
        if (isset($typeEntities[$handler])) {
            return $typeEntities[$handler];
        } else {
            require_once(self::get('ILEXPATH') . 'base/' . $type . '/Base.php');
            require(self::get('APPPATH') . $type . '/' . $handler . '.php');
            $className = $handler . ucfirst($type);
            $class = new $className;
            return self::letTo($type, $handler, $class);
        }
    }

}