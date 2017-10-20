<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 20/10/2017
 * Time: 08:26
 */

namespace Repository\Mapper;

use Psr\Container\ContainerInterface;
use Repository\Entity\Entity;
use Repository\Entity\EntityInterface;
use Repository\Mapper\Feature\FeatureInterface;
use Repository\Repository\RepositoryPluginManager;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\EventManager\EventManagerInterface;
use Zend\Hydrator\HydratorInterface;


/**
 * Class StandardMapper
 * @package Repository\Mapper
 * @method self withRelations(array $reps)
 * @method Entity relate(EntityInterface $entity)
 * @method Entity recover(int $id)
 * @method self withTransaction()
 * @method self commitTransaction()
 */
interface MapperInterface
{
    /**
     * Set hydrator
     *
     * @param  HydratorInterface $hydrator
     * @return static
     */
    public function setHydrator(HydratorInterface $hydrator);

    /**
     * Retrieve hydrator
     *
     * @param void
     * @return null|HydratorInterface
     * @access public
     */
    public function getHydrator();

    /** @return EventManagerInterface */
    public function getEventManager();

    /** @return TableGateway */
    public function getGateway();

    /** @return ContainerInterface */
    public function getRepository();

    /** @param ContainerInterface $repository */
    public function setRepository(ContainerInterface $repository);

    public static function getAdapterClass(): string;

    public static function setAdapterClass(string $adapterClass);

    public static function getHydratorClass(): string;

    public static function setHydratorClass(string $hydratorClass);

    public static function getFeatures(): array;
    public static function setFeatures(array $features);

    /**
     * @return mixed
     */
    public static function getTable();


    public function registerFeature(FeatureInterface $feature, $options = null);

    /**
     * @param string $name
     * @param callable $method
     * @return static
     */
    public function addFeatureMethod($name, callable $method);


    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     */
    public function save(EntityInterface $entity);

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     */
    public function update(EntityInterface $entity);

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     */
    public function insert(EntityInterface $entity);

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function delete(EntityInterface $entity);

    /**
     * @param Select|array $select
     * @return null|ResultSetInterface
     */
    public function fetch($select = []);

    /**
     * @param Select|array $select
     * @return null|EntityInterface
     */
    public function fetchOne($select);

    /**
     * @param mixed $id
     * @return EntityInterface
     */
    public function id($id);

    /**
     * @return Select
     */
    public function getSelect();

    /**
     * @return Select
     */
    public function createSelect();

    /**
     * @return EntityInterface
     */
    public function createModel();

    /**
     * @param Select $select
     * @return static
     */
    public function setSelect(Select $select);
}