<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 18/10/2017
 * Time: 11:37
 */

namespace Repository\Mapper\Feature;


use Repository\Entity\Entity;
use Repository\Entity\EntityInterface;
use Repository\Mapper\MapperInterface;
use Repository\Mapper\StandardMapper;
use Zend\EventManager\EventInterface;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;
use Zend\Hydrator\StrategyEnabledInterface;

class SoftDelete implements FeatureInterface
{
    protected $field = 'deleted_at';

    public function register(MapperInterface $mapper, $options = null)
    {
        if (is_string($options)) {
            $this->field = $options;
        }

        /** @var StrategyEnabledInterface $hydrator */
        $hydrator = $mapper->getHydrator();
        $select   = $mapper->getSelect();
        $select->where->isNull($this->field);
        $mapper->setSelect($select);

        $mapper->addFeatureMethod('recover', [$this, 'recover']);

        $hydrator->addStrategy($this->field, new DateTimeFormatterStrategy('Y-m-d H:i:s'));

        $this->attach($mapper);
    }

    /**
     * @param MapperInterface $mapper
     * @param $id
     * @return null|EntityInterface
     */
    public function recover(MapperInterface $mapper, $id)
    {
        $select = $mapper->createSelect();
        $select->where->isNotNull($this->field)->equalTo('id', $id);
        $entity = $mapper->fetchOne($select);

        if ($entity instanceof Entity) {
            $entity->{$this->field} = null;

            $mapper->update($entity);
        }

        return $entity;
    }

    /**
     * @param MapperInterface $mapper
     */
    protected function attach(MapperInterface $mapper)
    {
        $mapper->getEventManager()->attach('pre.delete', function (EventInterface $event) use ($mapper) {
            /** @var EntityInterface $entity */
            $entity = $event->getTarget();

            $entity->{$this->field} = new \DateTime();
            $mapper->update($entity);
            $event->stopPropagation(true);
        });
    }
}