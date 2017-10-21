<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 21/10/2017
 * Time: 21:25
 */

namespace Repository\Mapper\SelectStrategy;


use Zend\Db\Sql\Select;
use Zend\ServiceManager\AbstractPluginManager;

class SelectStrategyPluginManager extends AbstractPluginManager implements SelectStrategyInterface
{
    public static $key = 'select_strategy';
    protected $instanceOf = SelectStrategyInterface::class;

    /**
     * @param Select $select
     * @param mixed $data
     * @param mixed $entity
     * @return Select
     */
    public function select(Select $select, $data = null, $entity = null): Select
    {
        foreach ($data as $pluginName => $pluginData) {
            if ($this->has($pluginName)) {
                $plugin = $this->get($pluginName);
                $plugin->select($select, $pluginData);
            } else {
                $select->where($pluginData);
            }
        }

        return $select;
    }
}