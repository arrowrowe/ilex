<?php


namespace Ilex\Core;


class Constant
{
    public static function initialize()
    {
        $constants = array(

            /*
             * -----------------------
             * System
             * -----------------------
             */

            'SYS_SESSNAME'          => 'ILEX_SESSION',
            'ENV_HOST'              => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost',

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
        include_once(Loader::APPPATH() . 'config/const.php');
        foreach ($constants as $name => $value) {
            if (!defined($name)) {
                define($name, $value);
            }
        }
    }

}
