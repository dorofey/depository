<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 21/10/2017
 * Time: 21:40
 */

namespace Repository\Mapper\SelectStrategy\Strategies;


use Repository\Mapper\SelectStrategy\SelectStrategyInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class Random implements SelectStrategyInterface
{

    /**
     * @param Select $select
     * @param mixed $data
     * @param mixed $entity
     * @return Select
     */
    public function select(Select $select, $data = null, $entity = null): Select
    {
        $rand = uniqid('rand', false);
        $select->columns(['*', $rand => new Expression('RAND()')]);
        $select->order($rand);

        return $select;

    }
}