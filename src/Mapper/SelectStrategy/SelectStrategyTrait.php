<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 18/10/2017
 * Time: 09:28
 */

namespace Repository\Mapper\SelectStrategy;


trait SelectStrategyTrait
{
    /** @var  SelectStrategyInterface */
    protected $selectStrategy;

    /**
     * @param SelectStrategyInterface $selectStrategy
     * @return SelectStrategyTrait
     */
    public function setSelectStrategy(SelectStrategyInterface $selectStrategy): SelectStrategyTrait
    {
        $this->selectStrategy = $selectStrategy;

        return $this;
    }

    /**
     * @return SelectStrategyInterface
     */
    public function getSelectStrategy(): SelectStrategyInterface
    {
        return $this->selectStrategy;
    }
}