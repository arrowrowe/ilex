<?php


namespace Ilex\Base\Controller;

use \Ilex\Core\Loader;


/**
 * Class Base
 * @package Ilex\Base\Controller
 */
class Base
{
    protected function load_model($path)
    {
        $name = Loader::getHandlerFromPath($path);
        return is_null($this->$name) ? ($this->$name = Loader::model($path)) : $this->$name;
    }
}