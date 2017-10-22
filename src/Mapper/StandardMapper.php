<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 18/10/2017
 * Time: 09:38
 */

namespace Repository\Mapper;


use Psr\Container\ContainerInterface;
use Repository\Entity\EntityInterface;
use Repository\Hydrator\PublicProperties;
use Repository\Repository\RepositoryPluginManager;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\Adapter\AdapterAwareTrait;
use Zend\Db\ResultSet\AbstractResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\HydratorAwareInterface;
use Zend\Hydrator\HydratorAwareTrait;
use Repository\Mapper\Feature\FeatureTrait;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class StandardMapper
 * @package Repository\Mapper
 * @method static withRelations(array $reps)
 * @method EntityInterface relate(EntityInterface $entity)
 * @method EntityInterface recover(int $id)
 * @method static withTransaction()
 * @method static commitTransaction()
 * @method CachingResultSet fetchByStrategy(array $data)
 * @method EntityInterface fetchOneByStrategy(array $data)
 */
class StandardMapper implements AdapterAwareInterface, HydratorAwareInterface, MapperInterface
{
    use HydratorAwareTrait;
    use AdapterAwareTrait;
    use EventManagerAwareTrait;
    use FeatureTrait;

    /** @var  TableGateway */
    protected $gateway;
    /** @var  Select */
    protected $select;
    /** @var  RepositoryPluginManager */
    protected $repository;

    protected static $entityClass;
    protected static $table;
    protected static $hydratorClass = PublicProperties::class;
    protected static $adapterClass = Adapter::class;

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     */
    public function save(EntityInterface $entity)
    {
        $result = $this->getEventManager()->trigger('pre.' . __FUNCTION__, $entity);

        if (!$result->stopped()) {
            $entity = $entity->getId() === null ? $this->insert($entity) : $this->update($entity);
        }

        $this->getEventManager()->trigger('post.' . __FUNCTION__, $entity);

        return $entity;
    }

    public function update(EntityInterface $entity)
    {
        $result = $this->getEventManager()->trigger('pre.' . __FUNCTION__, $entity);
        if (!$result->stopped()) {
            $this->getGateway()->update($this->getHydrator()->extract($entity), ['id' => $entity->getId()]);
        }
        $this->getEventManager()->trigger('post.' . __FUNCTION__, $entity);

        return $entity;
    }

    public function insert(EntityInterface $entity)
    {
        $result = $this->getEventManager()->trigger('pre.' . __FUNCTION__, $entity);
        if (!$result->stopped()) {
            $this->getGateway()->insert($this->getHydrator()->extract($entity));
            $entity->setId($this->getGateway()->getLastInsertValue());
        }
        $this->getEventManager()->trigger('post.' . __FUNCTION__, $entity);

        return $entity;
    }

    public function delete(EntityInterface $entity)
    {
        $result = $this->getEventManager()->trigger('pre.' . __FUNCTION__, $entity);
        if (!$result->stopped()) {
            $this->getGateway()->delete(['id' => $entity->getId()]);
        }
        $this->getEventManager()->trigger('post.' . __FUNCTION__, $entity);
    }

    /**
     * @param mixed $select
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function fetch($select = [])
    {
        if (is_array($select)) {
            $select = $this->getSelect()->where($select);
        }

        if (is_callable($select)) {
            $_select = $this->getSelect();
            $select($_select);
            $select = $_select;
        }

        if (!($select instanceof Select)) {
            $select = $this->getSelect()->where($select);
        }

        $this->getEventManager()->trigger('pre.' . __FUNCTION__, $select);

        $result = $this->getGateway()->selectWith($select);

        $this->getEventManager()->trigger('post.' . __FUNCTION__, $result);

        return $result;
    }

    /**
     * @param Select|array $select
     * @return EntityInterface|null
     */
    public function fetchOne($select)
    {
        if (is_array($select)) {
            $select = $this->getSelect()->where($select);
        }

        if (is_callable($select)) {
            $_select = $this->getSelect();
            $select($_select);
            $select = $_select;
        }

        if (!($select instanceof Select)) {
            $select = $this->getSelect()->where($select);
        }

        $this->getEventManager()->trigger('pre.' . __FUNCTION__, $select);
        $result = $this->getGateway()->selectWith($select->limit(1));
        $this->getEventManager()->trigger('post.' . __FUNCTION__, $result);


        if ($result instanceof AbstractResultSet) {
            return $result->current();
        }

        return null;
    }

    public function id($id)
    {
        $this->getEventManager()->trigger('pre.' . __FUNCTION__, $id);
        $entity = $this->fetchOne(['id' => $id]);
        $this->getEventManager()->trigger('post.' . __FUNCTION__, $entity);

        return $entity;
    }


    /**
     * @return mixed
     */
    public static function getEntityClass()
    {
        return static::$entityClass;
    }

    /**
     * @param mixed $entityClass
     */
    public static function setEntityClass($entityClass)
    {
        static::$entityClass = $entityClass;
    }

    /**
     * @return mixed
     */
    public static function getTable()
    {
        return static::$table;
    }

    /**
     * @param mixed $table
     */
    public static function setTable($table)
    {
        static::$table = $table;
    }

    /**
     * @return string
     */
    public static function getHydratorClass(): string
    {
        return static::$hydratorClass;
    }

    /**
     * @param string $hydratorClass
     */
    public static function setHydratorClass(string $hydratorClass)
    {
        static::$hydratorClass = $hydratorClass;
    }

    /**
     * @return string
     */
    public static function getAdapterClass(): string
    {
        return static::$adapterClass;
    }

    /**
     * @param string $adapterClass
     */
    public static function setAdapterClass(string $adapterClass)
    {
        static::$adapterClass = $adapterClass;
    }

    public function getSelect()
    {
        if (null === $this->select) {
            $this->select = $this->createSelect();
        }

        return clone $this->select;
    }

    public function createSelect()
    {
        return $this->getGateway()->getSql()->select();
    }

    public function createModel()
    {
        return new static::$entityClass;
    }

    /**
     * @return TableGateway
     */
    public function getGateway(): TableGateway
    {
        if (null === $this->gateway) {

            $resultSet = $this->getHydrator()
                ? new CachingResultSet($this->getHydrator(), $this->createModel())
                : null;

            $this->gateway = new TableGateway(static::$table, $this->adapter, null, $resultSet);
        }

        return $this->gateway;
    }

    /**
     * @param TableGateway $gateway
     */
    public function setGateway(TableGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @param Select $select
     * @return StandardMapper
     */
    public function setSelect(Select $select): StandardMapper
    {
        $this->select = $select;

        return $this;
    }

    /**
     * @return RepositoryPluginManager
     */
    public function getRepository(): RepositoryPluginManager
    {
        return $this->repository;
    }

    /**
     * @param ContainerInterface $repository
     */
    public function setRepository(ContainerInterface $repository)
    {
        $this->repository = $repository;
    }

}