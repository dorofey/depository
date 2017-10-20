<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 20/10/2017
 * Time: 11:00
 */

namespace Repository\Hydrator;


use Repository\Mapper\MapperInterface;
use Zend\Hydrator\Strategy\StrategyInterface;

class HasManyThrough implements StrategyInterface
{
    /** @var MapperInterface */
    protected $mapper;
    protected $entityName;
    /** @var string */
    private $masterField;
    /** @var string */
    private $slaveField;
    /**
     * @var mixed
     */
    private $table;

    /**
     * Relation constructor.
     * @param MapperInterface $mapper
     * @param string $entityName
     * @param array $fields
     * @param null $table
     */
    public function __construct(MapperInterface $mapper, $entityName, array $fields, $table = null)
    {
        $this->mapper     = $mapper;
        $this->entityName = $entityName;
        list($this->masterField, $this->slaveField) = $fields;
        $this->table = $table ?? implode('_', $fields);
    }


    public function extract($value)
    {
        return false;
    }

    /**
     * @param mixed $value
     * @param null $data
     * @return bool|mixed|null|\Zend\Db\ResultSet\ResultSetInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function hydrate($value, $data = null)
    {
        if (!$data['id']) {
            return false;
        }
        /** @var MapperInterface $mapper */
        $mapper = $this->mapper->getRepository()->get($this->entityName);

        $select = $mapper->getSelect();
        $join   = sprintf('%s.id = %s.%s', $mapper::getTable(), $this->table, $this->slaveField);
        $select->join($this->table, $join, []);
        $select->where([$this->table . '.' . $this->masterField => $data['id']]);

        return $mapper->fetch($select);

    }
}