<?php


namespace Ilex\Base\Model\sys;

use Ilex\Base\Model\Base;


/**
 * Class Session
 * @package Ilex\Base\Model\sys
 *
 * @property string sid
 * @property string uid
 * @property string username
 * @property bool login
 */
class Session extends Base
{
    const SID                   = 'sid';
    const UID                   = 'uid';
    const USERNAME              = 'username';
    const LOGIN                 = 'login';

    public function __construct()
    {
        $this->start();
        if (!$this->has(self::SID)) {
            $this->newSid();
        }
        if (!$this->has(self::UID)) {
            $this->makeGuest();
        }
    }

    protected function start()
    {
        session_name(SYS_SESSNAME);
        session_start();
    }

    public function forget()
    {
        session_unset();
        session_destroy();
        $this->start();
        $this->newSid();
        $this->makeGuest();
    }

    public function makeGuest()
    {
        $this->assign(array(
            self::UID               => 0,
            self::USERNAME          => 'Guest',
            self::LOGIN             => FALSE
        ));
    }

    public function newSid()
    {
        return $this->sid = sha1(uniqid().mt_rand());
    }

    public function assign($vars)
    {
        $_SESSION = array_merge($_SESSION, $vars);
    }

    public function has()
    {
        foreach (func_get_args() as $k) {
            if (!isset($_SESSION[$k])) {
                return FALSE;
            }
        }
        return TRUE;
    }

    public function __get($k)
    {
        return $this->get($k);
    }

    public function get($k, $default = FALSE)
    {
        return isset($_SESSION[$k]) ? $_SESSION[$k] : $default;
    }

    public function __set($k, $v)
    {
        $this->let($k, $v);
    }

    public function let($k, $v)
    {
        return $_SESSION[$k] = $v;
    }

}