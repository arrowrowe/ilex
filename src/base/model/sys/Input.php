<?php


namespace Ilex\Base\Model\sys;

use Ilex\Base\Model\Base;


/**
 * Class Input
 * @package Ilex\Base\Model\sys
 */
class Input extends Base
{
    public $get;
    public $post;

    public function __construct()
    {
        $this->get = new Container($_GET);
        $this->post = new Container($_POST);
    }

    public function merge($name, $data = array())
    {
        $this->$name->merge($data);
    }

    public function clear($name = '')
    {
        if ($name) {
            $this->$name->assign();
        } else {
            $this->get->assign();
            $this->post->assign();
        }
        return $this;
    }

    public function get($key = NULL, $default = NULL) { return $this->get->get($key, $default); }
    public function post($key = NULL, $default = NULL) { return $this->post->get($key, $default); }
}
