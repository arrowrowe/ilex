<?php


namespace Ilex\Base\Controller;

use \Ilex\Core\Loader;


/**
 * Class Base
 */
class Base
{
    protected $db = NULL;
    public $last_error = array();
    public $last_error_message = '';

    protected function load_db()
    {
        return is_null($this->db) ? ($this->db = Loader::db()) : $this->db;
    }

    protected function load_model($path)
    {
        $name = Loader::getHandlerFromPath($path);
        return is_null($this->$name) ? ($this->$name = Loader::model($path)) : $this->$name;
    }
}