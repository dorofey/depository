<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 18/10/2017
 * Time: 09:17
 */

namespace Repository\Hydrator;

use Zend\Hydrator\AbstractHydrator;

class PublicProperties extends AbstractHydrator
{
    protected $cacheMap = [];

    /**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
     */
    public function extract($object)
    {
        $data   = get_object_vars($object);
        $filter = $this->getFilter();

        foreach ($data as $name => $value) {
            if (! $filter->filter($name)) {
                unset($data[$name]);
                continue;
            }

            $extracted = $this->extractName($name, $object);

            if ($extracted !== $name) {
                unset($data[$name]);
                $name = $extracted;
            }

            $value = $this->extractValue($name, $value, $object);

            if ($value === false) {
                unset($data[$name]);
            } else {
                $data[$name] = $value;
            }
        }

        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        $strategies = array_flip(array_keys($this->strategies->getArrayCopy()));

        $properties = $this->getObjectFields($object);
        $data       = array_intersect_key($data, $properties);

        foreach ($data as $property => $datum) {
            $property            = $this->hydrateName($property, $data);
            $object->{$property} = $this->hydrateValue($property, $datum, $data);

            unset($strategies[$property]);
        }

        foreach ($strategies as $property => $v) {
            $value = $this->hydrateValue($property, null, $data);
            if ($value !== false) {
                $object->{$property} = $value;
            }
        }

        return $object;
    }

    protected function getObjectFields($object)
    {
        $className = get_class($object);
        if (! array_key_exists($className, $this->cacheMap)) {
            $this->cacheMap[$className] = get_class_vars($className);
        }

        return $this->cacheMap[$className];
    }
}
