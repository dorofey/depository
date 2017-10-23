<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 23/10/2017
 * Time: 09:37
 */

namespace Repository\Mapper\Feature;

interface FeatureAwareInterface
{
    /**
     * @return array
     */
    public static function getFeatures(): array;

    /**
     * @param array $features
     * @return mixed
     */
    public static function setFeatures(array $features);

    /**
     * @param FeatureInterface $feature
     * @param null|array $options
     * @return mixed
     */
    public function registerFeature(FeatureInterface $feature, $options = null);

    /**
     * @param string $name
     * @param callable $method
     * @return static
     */
    public function addFeatureMethod($name, callable $method);
}
