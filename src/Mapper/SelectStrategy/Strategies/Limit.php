<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 21/10/2017
 * Time: 22:11
 */

namespace Repository\Mapper\SelectStrategy\Strategies;


use Repository\Mapper\SelectStrategy\SelectStrategyInterface;
use Zend\Db\Sql\Select;

class Limit implements SelectStrategyInterface
{

    /**
     * @param Select $select
     * @param mixed $data
     * @param mixed $entity
     * @return Select
     */
    public function select(Select $select, $data = null, $entity = null): Select
    {
        if ($data) {
            $select->limit((int)$data);
        }

        return $select;
    }
}