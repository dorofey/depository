<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 21/10/2017
 * Time: 22:12
 */

namespace Repository\Mapper\SelectStrategy\Strategies;

use Repository\Mapper\SelectStrategy\SelectStrategyInterface;
use Zend\Db\Sql\Select;

class Offset implements SelectStrategyInterface
{

    /**
     * @param Select $select
     * @param mixed $data
     * @param mixed $entity
     * @return Select
     */
    public function select(Select $select, $data = null, $entity = null): Select
    {
        $select->offset((int)$data);
        if (! $select->getRawState('limit')) {
            // MySQL needs a limit when offset it set ... set an limit:
            $select->limit(PHP_INT_MAX);
        }

        return $select;
    }
}
