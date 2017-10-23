<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 18/10/2017
 * Time: 14:44
 */

namespace Repository\Mapper\Feature;


use Repository\Entity\EntityInterface;
use Repository\Mapper\CachingResultSet;
use Repository\Mapper\MapperInterface;
use Zend\Hydrator\AbstractHydrator;

class Relations implements FeatureInterface
{

    protected $relations = [];

    public function register(MapperInterface $mapper, $options = null)
    {
        foreach ((array)$options as $name => $relation) {
            $relationClass          = array_shift($relation);
            $this->relations[$name] = new $relationClass($mapper, ...$relation);
        }

        if ($mapper instanceof FeatureAwareInterface) {
            $mapper->addFeatureMethod('relate', [$this, 'relate']);
            $mapper->addFeatureMethod('withRelations', [$this, 'withRelations']);
        }
    }

    public function withRelations(MapperInterface $mapper, array $with = [])
    {
        /** @var AbstractHydrator $hydrator */
        $hydrator = clone $mapper->getHydrator();

        foreach ($with as $item) {
            if (array_key_exists($item, $this->relations)) {
                $hydrator->addStrategy($item, $this->relations[$item]);
            }
        }

        $result = new FeatureProxy($mapper, function ($_result) use ($mapper, $hydrator, $with) {
            if ($hydrator && $_result instanceof CachingResultSet) {
                $_result->setHydrator($hydrator);
            }

            if ($_result instanceof EntityInterface) {
                $this->relate($mapper, $_result, $with);
            }

            return $_result;
        });

        return $result;
    }

    /**
     * @param MapperInterface $mapper
     * @param EntityInterface $entity
     * @param array $relations
     * @return EntityInterface
     */
    public function relate(MapperInterface $mapper, EntityInterface $entity, array $relations = [])
    {
        foreach ($relations as $relation) {
            if (array_key_exists($relation, $this->relations)) {
                /** @var \Zend\Hydrator\Strategy\StrategyInterface $strategy */
                $strategy            = $this->relations[$relation];
                $entity->{$relation} = $strategy->hydrate($entity->{$relation});
            }
        }

        return $entity;
    }
}