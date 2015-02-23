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

    public function load_db()
    {
        return is_null($this->db) ? ($this->db = Ilex\Loader::db()) : $this->db;
    }

    public function selectCollection($name)
    {
        return $this->collection = $this->load_db()->selectCollection($name);
    }

}