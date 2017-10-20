<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 20/10/2017
 * Time: 08:41
 */

namespace Repository\Mapper\Feature;


class FeatureProxy
{
    public $mapper;
    /**
     * @var callable
     */
    private $callback;

    /**
     * FeatureProxy constructor.
     * @param $mapper
     * @param callable|null $callback
     */
    public function __construct($mapper, callable $callback = null)
    {
        $this->mapper = $mapper;
        $this->callback = $callback;
    }

    public function __call($method, $parameters)
    {
        $result = call_user_func_array([$this->mapper, $method], $parameters);

        if($this->callback) {
            $result = call_user_func($this->callback, $result);
        }

        return $result;
    }
}