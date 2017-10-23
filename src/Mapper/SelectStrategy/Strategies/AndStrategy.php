<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 21/10/2017
 * Time: 22:06
 */

namespace Repository\Mapper\SelectStrategy\Strategies;


use Repository\Mapper\SelectStrategy\SelectStrategyException;
use Repository\Mapper\SelectStrategy\SelectStrategyInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class AndStrategy implements SelectStrategyInterface
{


    /**
     * @param Select $select
     * @param null $data
     * @param null $entity
     * @return Select
     * @throws SelectStrategyException
     */
    public function select(Select $select, $data = null, $entity = null): Select
    {
        $chunks = is_array($data)
            ? $data
            : explode(',', $data);
        $where  = new Where();
        $where  = $where->NEST;

        foreach ($chunks as $chunk) {

            $chunk = trim($chunk);

            if (preg_match(
                '/(?P<left>[\w|\.]+)\s*(?P<operator>!=|<>|=|<|>|<=|=>)\s*(?P<right>.+)/',
                $chunk,
                $result
            )) {

                $result['right'] = trim($result['right'], "'\"");
                $result['left']  = trim($result['left'], "'\"");

                // {where:["some<12", "other='12'"]}
                // {where:"some<12 , other='12'"}
                switch ($result['operator']) {
                    case '<':
                        $where->lessThan($result['left'], $result['right']);
                        break;
                    case '<=':
                        $where->lessThanOrEqualTo($result['left'], $result['right']);
                        break;
                    case '>':
                        $where->greaterThan($result['left'], $result['right']);
                        break;
                    case '=>':
                        $where->greaterThanOrEqualTo($result['left'], $result['right']);
                        break;
                    case '<>':
                    case '!=':
                        $where->notEqualTo($result['left'], $result['right']);
                        break;
                    case '=':
                    default:
                        $where->equalTo($result['left'], $result['right']);
                        break;
                }
                if ($this instanceof AndStrategy) {
                    $where->and;
                }
                if ($this instanceof OrStrategy) {
                    $where->or;
                }
            } else {
                throw new SelectStrategyException(
                    sprintf(
                        'This chunk of the where-clause does not parse: "%s"',
                        $chunk
                    )
                );
            }
        }

        $where->unnest();
        $select->where($where);

        return $select;

    }
}