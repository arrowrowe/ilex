<?php


namespace Ilex\Base\Model\sys;

use Ilex\Base\Model\Base;
use Ilex\Core;


/**
 * Class Session
 * @package Ilex\Base\Model\sys
 *
 * @property string sid
 * @property string uid
 * @property string username
 * @property bool login
 *
 * @todo Should I remove this model?......
 */
class Session extends Base
{
    const SID       = Core\Session::SID;
    const UID       = Core\Session::UID;
    const USERNAME  = Core\Session::USERNAME;
    const LOGIN     = Core\Session::LOGIN;

    public function __construct()
    {
        Core\Session::boot();
    }

    protected function start()
    {
        Core\Session::start();
    }

    public function forget()
    {
        Core\Session::forget();
    }

    public function makeGuest()
    {
        Core\Session::makeGuest();
    }

    public function newSid()
    {
        return Core\Session::newSid();
    }

    public function assign($vars)
    {
        Core\Session::assign($vars);
    }

    public function has($key)
    {
        return Core\Session::has($key);
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function get($key = FALSE, $default = FALSE)
    {
        return Core\Session::get($key, $default);
    }

    public function __set($key, $value)
    {
        return Core\Session::let($key, $value);
    }

}