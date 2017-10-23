<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 18/10/2017
 * Time: 14:38
 */

namespace Repository\Mapper\Feature;

use Repository\Mapper\MapperInterface;

interface FeatureInterface
{
    public function register(MapperInterface $mapper, $options = null);
}
