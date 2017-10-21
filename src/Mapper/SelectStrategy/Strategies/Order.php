<?php

namespace Repository\Mapper\SelectStrategy\Strategies;

use Repository\Mapper\SelectStrategy\SelectStrategyInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class Order implements SelectStrategyInterface
{

    /**
     * @param Select $select
     * @param mixed $data
     * @param mixed $entity
     * @return Select
     */
    public function select(Select $select, $data = null, $entity = null): Select
    {
        $orderArray = [];

        if (!is_array($data)) {
            $data = explode(',', $data);
        }

        foreach ($data as $chunk) {
            if (is_string($chunk)) {

                $dir = 'ASC';
                if (strpos($chunk, '-') === 0) {
                    $dir   = 'DESC';
                    $chunk = substr($chunk, 1);
                }
                $orderArray[] = $chunk . ' ' . $dir;

            } elseif (is_array($chunk)) {

                $dir   = 'ASC';
                $field = null;

                if (isset($chunk['field'])) {
                    $field = $chunk['field'];
                }

                if (strpos($field, '-') === 0) {
                    $dir   = 'DESC';
                    $field = substr($field, 1);
                }

                // explicit 'dir' should take priority over '-'
                if (isset($chunk['dir'])) {
                    $dir = $chunk['dir'];
                }

                if (isset($chunk['cast']) && $field) {
                    // Expression?
                    $field = sprintf('CAST(`%s` AS %s)', $field, strtoupper($chunk['cast']));
                }

                if (isset($chunk['collate']) && $field) {
                    $field = sprintf('%s COLLATE \'%s\'', $field, $chunk['collate']);
                }

                if (isset($chunk['coalesce']) && $field) {
                    $field = sprintf("IF(COALESCE(`%s`, '') = '', NOW(), COALESCE(`%s`))", $field, $field);
                }

                $orderArray[] = new Expression($field . ' ' . $dir);
            }
        }

        if ($orderArray) {
            $select->order($orderArray);
        }

        return $select;
    }
}