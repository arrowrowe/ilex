<?php


namespace Ilex\Base;

use Ilex\Core\Loader;


class Base
{
    protected function load_model($path)
    {
        $name = Loader::getHandlerFromPath($path);
        return is_null($this->$name) ? ($this->$name = Loader::model($path)) : $this->$name;
    }
}