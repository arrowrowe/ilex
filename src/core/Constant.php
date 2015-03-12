<?php


namespace Ilex\Core;


class Constant
{
    protected static $constants = array(

        /*
         * -----------------------
         * System
         * -----------------------
         */

        'SYS_SESSNAME'          => 'ILEX_SESSION',

        'ENVIRONMENT'           => 'DEVELOPMENT',

        /*
         * -----------------------
         * Server
         * -----------------------
         */

        'SVR_MONGO_HOST'        => 'localhost',
        'SVR_MONGO_PORT'        => 27017,
        'SVR_MONGO_USER'        => 'admin',
        'SVR_MONGO_PASS'        => 'admin',
        'SVR_MONGO_DB'          => 'test',
        'SVR_MONGO_TIMEOUT'     => 2000,
    );

    public static function initialize()
    {
        include_once(Loader::APPPATH() . 'config/const.php');
        foreach (static::$constants as $name => $value) {
            if (!defined($name)) {
                define($name, $value);
            }
        }
    }

}
