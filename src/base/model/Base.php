<?php


/**
 * Class BaseModel
 * @property \MongoDB $db database
 */
class BaseModel
{
    protected $db = NULL;

    public static function escape($input)
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    public function load_db()
    {
        return is_null($this->db) ? ($this->db = Ilex\Loader::db()) : $this->db;
    }

}