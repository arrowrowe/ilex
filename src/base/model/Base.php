<?php


namespace Ilex\Base\Model;

use \Ilex\Core\Loader;


/**
 * Class Base
 * @package Ilex\Base\Model
 */
class Base
{
    public static function escape($input)
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    protected function load_model($path)
    {
        $name = Loader::getHandlerFromPath($path);
        return is_null($this->$name) ? ($this->$name = Loader::model($path)) : $this->$name;
    }
}