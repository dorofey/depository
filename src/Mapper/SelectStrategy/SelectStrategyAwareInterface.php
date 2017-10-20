<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 18/10/2017
 * Time: 10:53
 */

namespace Repository\Mapper\SelectStrategy;


interface SelectStrategyAwareInterface
{
    /**
     * @param SelectStrategyInterface $selectStrategy
     * @return SelectStrategyTrait
     */
    public function setSelectStrategy(SelectStrategyInterface $selectStrategy): SelectStrategyTrait;

    /**
     * @return SelectStrategyInterface
     */
    public function getSelectStrategy(): SelectStrategyInterface;

}