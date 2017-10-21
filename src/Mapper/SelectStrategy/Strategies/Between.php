<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 21/10/2017
 * Time: 22:09
 */

namespace Repository\Mapper\SelectStrategy\Strategies;


use Repository\Mapper\SelectStrategy\SelectStrategyInterface;
use Zend\Db\Sql\Select;

class Between implements SelectStrategyInterface
{

    /**
     * @param Select $select
     * @param mixed $data
     * @param mixed $entity
     * @return Select
     */
    public function select(Select $select, $data = null, $entity = null): Select
    {
        foreach ($data as $key => $value) {

            if (!is_array($value)) {
                $value = explode(',', $value);
            }

            // Can work as ['min'=> ..., 'max' => ...]
            $value = array_values($value);

            $select->where->expression('`' . $key . '` BETWEEN CAST(? AS UNSIGNED) AND CAST(? AS UNSIGNED)', $value);
        }

        return $select;
    }
}