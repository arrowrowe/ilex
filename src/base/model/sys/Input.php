<?php


class InputModel extends BaseModel
{
    public $get;
    public $post;

    public function __construct()
    {
        $this->get = new ArrayModel($_GET);
        $this->post = new ArrayModel($_POST);
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


class ArrayModel
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