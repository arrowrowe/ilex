<?php


namespace Ilex\Base;

use Ilex\Core\Loader;


class Base
{
    protected function load_model()
    {
        $params = array();
        foreach (func_get_args() as $index => $n) {
            if ($index == 0) {
                $path = $n;
            } else {
                $params[] = $n;
            }
        }
        $name = Loader::getHandlerFromPath($path);
        return is_null($this->$name) ? ($this->$name = Loader::model($path, $params)) : $this->$name;
    }
}