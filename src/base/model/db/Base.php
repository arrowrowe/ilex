<?php


namespace Ilex\Base\Model\db;

use Ilex\Core\Loader;


/**
 * Class Base
 * @package Ilex\Base\Model\db
 *
 * @property string $collectionName
 * @property \MongoCollection $collection
 */
class Base extends \Ilex\Base\Model\Base
{
    protected $collectionName;
    public $collection; // todo: Do NOT expose this

    public function __construct()
    {
        $this->collection = Loader::db()->selectCollection($this->collectionName);
    }
}