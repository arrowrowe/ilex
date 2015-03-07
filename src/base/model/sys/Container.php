<?php

namespace Ilex\Base\Model\sys;


class Container
{
    private $array;

    public function __construct($array)
    {
        $this->assign($array);
    }

    public function has()
    {
        foreach (func_get_args() as $key) {
            if (!isset($this->array[$key])) {
                return FALSE;
            }
        }
        return TRUE;
    }

    public function __get($key)
    {
        return $this->get($key, NULL);
    }

    public function get($key, $default)
    {
        return is_null($key) ?
            $this->$array :
            (isset($this->array[$key]) ? $this->array[$key] : $default);
    }

    public function merge($data)
    {
        $this->assign(array_merge($this->array, $data));
    }

    public function assign($data = array())
    {
        $this->array = $data;
    }
}