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
    private $array;

    public function __construct()
    {
        $this->start();
        if (ENVIRONMENT !== 'TEST') {
            $this->array = &$_SESSION;
        } else {
            $this->array = array();
        }
        if (!$this->has(self::SID)) {
            $this->newSid();
        }
        if (!$this->has(self::UID)) {
            $this->makeGuest();
        }
    }

    protected function start()
    {
        if (ENVIRONMENT !== 'TEST') {
            session_name(SYS_SESSNAME);
            session_start();
        }
    }

    public function forget()
    {
        if (ENVIRONMENT !== 'TEST') {
            session_unset();
            session_destroy();
        }
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
        $tmp = array_merge($this->array, $vars);
        if (ENVIRONMENT !== 'TEST') {
            $_SESSION = $tmp;
            $this->array = &$_SESSION;
        } else {
            $this->array = $tmp;
        }
    }

    public function has()
    {
        foreach (func_get_args() as $k) {
            if (!isset($this->array[$k])) {
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
        return isset($this->array[$k]) ? $this->array[$k] : $default;
    }

    public function __set($k, $v)
    {
        $this->let($k, $v);
    }

    public function let($k, $v)
    {
        return $this->array[$k] = $v;
    }

}