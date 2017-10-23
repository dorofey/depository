<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 23/10/2017
 * Time: 12:42
 */

namespace Repository;

class ConfigProvider
{
    /**
     * Retrieve BjyProfiler default configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies'    => $this->getDependencyConfig(),
            'select_strategy' => $this->getSelectStrategyConfig()
        ];
    }

    public function getDependencyConfig()
    {
        return require_once __DIR__ . '/../config/service_manager.config.php';
    }

    public function getSelectStrategyConfig()
    {
        return require_once __DIR__ . '/../config/select_strategy.config.php';
    }
}
