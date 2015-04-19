<?php


namespace Ilex\Core;


class Session
{
    const SID                   = 'sid';
    const UID                   = 'uid';
    const USERNAME              = 'username';
    const LOGIN                 = 'login';

    private static $booted = FALSE;
    private static $fakeSession;

    public static function boot()
    {
        if (!self::$booted) {
            self::start();
            self::$booted = TRUE;
            if (ENVIRONMENT !== 'TEST') {
                self::$fakeSession = &$_SESSION;
            } else {
                self::$fakeSession = array();
            }
            if (!static::has(static::SID)) {
                static::newSid();
            }
            if (!static::has(static::UID)) {
                static::makeGuest();
            }
        }
    }

    public static function start()
    {
        if (ENVIRONMENT !== 'TEST') {
            session_name(SYS_SESSNAME);
            session_start();
        }
    }

    public static function forget()
    {
        if (ENVIRONMENT !== 'TEST') {
            session_unset();
            session_destroy();
        }
        self::start();
        self::newSid();
        self::makeGuest();
    }

    public static function newSid()
    {
        return static::let(static::SID, sha1(uniqid().mt_rand()));
    }

    public static function makeGuest()
    {
        static::let(static::UID, 0);
        static::let(static::USERNAME, 'Guest');
        static::let(static::LOGIN, FALSE);
    }

    public static function has($key)
    {
        return isset(self::$fakeSession[$key]);
    }

    public static function get($key = FALSE, $default = FALSE)
    {
        return $key ?
            (isset(self::$fakeSession[$key]) ? self::$fakeSession[$key] : $default) :
            self::$fakeSession;
    }

    public static function let($key, $value)
    {
        return self::$fakeSession[$key] = $value;
    }

    public static function assign($vars)
    {
        $tmp = array_merge(self::$fakeSession, $vars);
        if (ENVIRONMENT !== 'TEST') {
            $_SESSION = $tmp;
            self::$fakeSession = &$_SESSION;
        } else {
            self::$fakeSession = $tmp;
        }
    }
}