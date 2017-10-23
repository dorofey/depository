<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 18/10/2017
 * Time: 21:11
 */

namespace Repository\Mapper\Feature;

use Repository\Mapper\MapperInterface;
use Repository\Mapper\SelectStrategy\SelectStrategyInterface;
use Repository\Mapper\StandardMapper;
use Zend\Db\Sql\Select;
use Zend\EventManager\EventInterface;

class SelectStrategy implements FeatureInterface
{
    /** @var SelectStrategyInterface */
    private $strategy;


    /**
     * SelectStrategy constructor.
     * @param SelectStrategyInterface $strategy
     */
    public function __construct(SelectStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    public function register(MapperInterface $mapper, $options = null)
    {
        if ($mapper instanceof FeatureAwareInterface) {
            $mapper->addFeatureMethod('fetchByStrategy', [$this, 'fetchByStrategy']);
            $mapper->addFeatureMethod('fetchOneByStrategy', [$this, 'fetchOneByStrategy']);
            $mapper->addFeatureMethod('withStrategy', [$this, 'withStrategy']);
        }
    }

    public function fetchByStrategy(MapperInterface $mapper, array $data = [])
    {
        $select = $mapper->getSelect();
        $this->strategy->select($select, $data);

        return $mapper->fetch($select);
    }

    public function fetchOneByStrategy(MapperInterface $mapper, array $data = [])
    {
        $select = $mapper->getSelect();
        $this->strategy->select($select, $data);

        return $mapper->fetchOne($select);
    }

    public function withStrategy(MapperInterface $mapper, array $data = [])
    {
        $listener = function (EventInterface $event) use ($mapper, $data, &$listener) {

            /** @var Select $select */
            $select = $event->getTarget();
            $this->strategy->select($select, $data);
            $mapper->getEventManager()->detach($listener);
        };

        $mapper->getEventManager()->attach('pre.fetchBySelect', $listener);
        $mapper->getEventManager()->attach('pre.fetchOneBySelect', $listener);

        return $mapper;
    }
}
