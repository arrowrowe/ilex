<?php


/**
 * Class BaseModel
 * @property \MongoDB $db database
 */
class BaseModel
{
    protected $db = NULL;
    protected $collection = NULL;

    public static function escape($input)
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    protected function load_db()
    {
        return is_null($this->db) ? ($this->db = Ilex\Loader::db()) : $this->db;
    }

    protected function load_model($path)
    {
        $name = \Ilex\Loader::getHandlerFromPath($path);
        return is_null($this->$name) ? ($this->$name = Ilex\Loader::model($path)) : $this->$name;
    }

    public function seliectCollection($name)
    {
        return $this->collection = $this->load_db()->selectCollection($name);
    }

}