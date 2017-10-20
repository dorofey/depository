<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 18/10/2017
 * Time: 11:47
 */

namespace Repository\Mapper;


use Zend\Db\ResultSet\HydratingResultSet;

class CachingResultSet extends HydratingResultSet
{
    public function toArray(): array
    {
        $return = [];

        foreach ($this as $row) {
            $return[] = $row;
        }

        return $return;
    }

}