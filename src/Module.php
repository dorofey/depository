<?php

namespace Repository;

class Module
{

    public function getConfig()
    {
        $config = new ConfigProvider();

        return [
            'service_manager' => $config->getDependencyConfig(),
            'select_strategy' => $config->getSelectStrategyConfig(),
        ];
    }
}
