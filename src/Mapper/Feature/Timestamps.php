<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 18/10/2017
 * Time: 11:13
 */

namespace Repository\Mapper\Feature;

use Repository\Hydrator\PublicProperties;
use Repository\Mapper\MapperInterface;
use Zend\EventManager\EventInterface;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;

class Timestamps implements FeatureInterface
{
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function register(MapperInterface $mapper, $options = null)
    {
        /** @var PublicProperties $hydrator */
        $hydrator = $mapper->getHydrator();

        $hydrator->addStrategy($this->createdField, new DateTimeFormatterStrategy('Y-m-d H:i:s'));
        $hydrator->addStrategy($this->updatedField, new DateTimeFormatterStrategy('Y-m-d H:i:s'));

        $mapper->getEventManager()->attach('pre.insert', function (EventInterface $event) {
            $event->getTarget()->{$this->createdField} = new \DateTime();
        });
        $mapper->getEventManager()->attach('pre.update', function (EventInterface $event) {
            $event->getTarget()->{$this->updatedField} = new \DateTime();
        });
    }
}