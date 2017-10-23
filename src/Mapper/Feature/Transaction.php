<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 18/10/2017
 * Time: 20:38
 */

namespace Repository\Mapper\Feature;


use Repository\Mapper\MapperInterface;

class Transaction implements FeatureInterface
{
    /**
     * @param MapperInterface $mapper
     * @param null $options
     */
    public function register(MapperInterface $mapper, $options = null)
    {
        if ($mapper instanceof FeatureAwareInterface) {
            $mapper->addFeatureMethod('withTransaction', [$this, 'withTransaction']);
            $mapper->addFeatureMethod('commitTransaction', [$this, 'commitTransaction']);
        }
    }

    /**
     * @param MapperInterface $mapper
     * @return MapperInterface
     */
    public function withTransaction(MapperInterface $mapper)
    {
        $mapper->getGateway()->getAdapter()->getDriver()->getConnection()->beginTransaction();

        return $mapper;
    }

    /**
     * @param MapperInterface $mapper
     * @return MapperInterface
     * @throws \Exception
     */
    public function commitTransaction(MapperInterface $mapper)
    {
        try {
            $mapper->getGateway()->getAdapter()->getDriver()->getConnection()->commit();
        } catch (\Exception $e) {
            $mapper->getGateway()->getAdapter()->getDriver()->getConnection()->rollback();
            throw $e;
        }

        return $mapper;
    }
}