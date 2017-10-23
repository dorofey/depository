<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 18/10/2017
 * Time: 13:05
 */

namespace Repository\Hydrator;

use Repository\Entity\EntityInterface;
use Repository\Mapper\MapperInterface;
use Zend\Db\ResultSet\AbstractResultSet;
use Zend\Db\Sql\Expression;
use Zend\Hydrator\Strategy\StrategyInterface;

class OneToMany implements StrategyInterface
{
    /** @var  MapperInterface */
    protected $mapper;
    protected $entityName;

    /**
     * Relation constructor.
     * @param MapperInterface $mapper
     * @param $entityName
     */
    public function __construct(MapperInterface $mapper, $entityName)
    {
        $this->mapper     = $mapper;
        $this->entityName = $entityName;
    }


    public function extract($value)
    {
        $result = [];

        if ($value instanceof AbstractResultSet) {
            $value = $value->toArray();
        }


        if (is_array($value)) {
            $result = array_map(function (EntityInterface $x) {
                return $x->getId();
            }, $value);
        }

        if ($value instanceof EntityInterface) {
            $result = [$value->id];
        }

        return implode(',', $result);
    }

    /**
     * @param mixed $value
     * @return mixed|null|\Zend\Db\ResultSet\ResultSetInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function hydrate($value)
    {
        $values = explode(',', $value);
        if (! count($values)) {
            return null;
        }

        /** @var MapperInterface $mapper */
        $mapper = $this->mapper->getRepository()->get($this->entityName);
        $select = $mapper->getSelect();

        $select->where->in('id', $values);
        $select->order(new Expression('FIELD(id, "' . implode('","', $values) . '")'));

        return $mapper->fetch($select);
    }
}
