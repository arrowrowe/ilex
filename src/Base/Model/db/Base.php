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

    protected function getId($id)
    {
        if (is_string($id)) {
            try {
                return new \MongoId($id);
            } catch (\Exception $e) {
                return $id;
            }
        } else {
            return $id;
        }
    }

    protected function setRetractId($object)
    {
        if (isset($object['_id'])) {
            $object['_id'] = $this->getId($object['_id']);
        }
        return $object;
    }

    public function find($criterion = [], $projection = [], $toArray = TRUE)
    {
        $criterion = $this->setRetractId($criterion);
        $cursor = $this->collection->find($criterion, $projection);
        return $toArray ? array_values(iterator_to_array($cursor)) : $cursor;
    }

}
