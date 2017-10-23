<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 20/10/2017
 * Time: 09:39
 */

namespace Repository\Hydrator;


use Repository\Mapper\MapperInterface;
use Zend\Hydrator\Strategy\StrategyInterface;

class HasMany implements StrategyInterface
{
    /** @var MapperInterface */
    protected $mapper;
    protected $entityName;
    /**
     * @var null|string
     */
    private $relationField;

    /**
     * Relation constructor.
     * @param MapperInterface $mapper
     * @param string $entityName
     * @param string $relationField
     */
    public function __construct(MapperInterface $mapper, $entityName, $relationField = null)
    {

        $this->mapper        = $mapper;
        $this->entityName    = $entityName;
        $this->relationField = $relationField;
    }


    public function extract($value)
    {
        return false;
    }

    /**
     * @param mixed $value
     * @param mixed $data
     * @return mixed|null|\Zend\Db\ResultSet\ResultSetInterface
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

        return $mapper->fetch([$this->relationField => $data['id']]);
    }
}