<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 22/10/2017
 * Time: 13:42
 */

namespace Repository\Mapper\Feature;


use Repository\Mapper\MapperInterface;

trait FeatureTrait
{
    protected $featureMethods = [];
    /** @var FeatureInterface[] */
    protected $registeredFeatures = [];

    protected static $features = [];

    /**
     * @return array
     */
    public static function getFeatures(): array
    {
        return static::$features;
    }

    /**
     * @param array $features
     */
    public static function setFeatures(array $features)
    {
        static::$features = $features;
    }

    public function registerFeature(FeatureInterface $feature, $options = null)
    {
        $this->registeredFeatures[] = $feature;
        if ($this instanceof MapperInterface) {
            $feature->register($this, $options);
        }
    }

    /**
     * @param FeatureInterface|string $feature
     * @return bool
     */
    public function hasFeature($feature)
    {
        if ($feature instanceof FeatureInterface) {
            return in_array($feature, $this->registeredFeatures, true);
        }

        if (is_string($feature)) {
            foreach ($this->registeredFeatures as $registeredFeature) {
                if (get_class($registeredFeature) === $feature) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $name
     * @param callable $method
     * @return $this
     */
    public function addFeatureMethod($name, callable $method)
    {
        $this->featureMethods[$name] = $method;

        return $this;
    }

    public function __call($name, $arguments)
    {
        if (array_key_exists($name, $this->featureMethods)) {
            array_unshift($arguments, $this);

            return call_user_func_array($this->featureMethods[$name], $arguments);
        }

        return false;
    }

}