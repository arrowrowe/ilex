<?php


namespace Ilex\Base\Model;

use \Ilex\Core\Loader;


/**
 * Class Base
 * @property \MongoDB $db database
 * @property \MongoCollection $collection
 */
class Base
{
    protected $db = NULL;
    protected $collection = NULL;

    public static function escape($input)
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    protected function load_db()
    {
        return is_null($this->db) ? ($this->db = Loader::db()) : $this->db;
    }

    protected function load_model($path)
    {
        $name = Loader::getHandlerFromPath($path);
        return is_null($this->$name) ? ($this->$name = Loader::model($path)) : $this->$name;
    }

    public function selectCollection($name)
    {
        return $this->collection = $this->load_db()->selectCollection($name);
    }
}