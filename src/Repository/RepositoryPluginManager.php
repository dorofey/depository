<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 18/10/2017
 * Time: 09:03
 */

namespace Repository\Repository;

use Zend\ServiceManager\AbstractPluginManager;

class RepositoryPluginManager extends AbstractPluginManager
{
    protected $maps = [];


    /**
     * @param array $maps
     * @return $this
     */
    public function setMaps(array $maps = [])
    {
        $this->maps = $maps;

        return $this;
    }

    /**
     * @return array
     */
    public function getMaps(): array
    {
        return $this->maps;
    }
}