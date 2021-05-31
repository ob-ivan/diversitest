<?php

namespace Ob_Ivan\DiversiTest;

use Ob_Ivan\DiversiTest\PackageManager\PackageManager;
use Ob_Ivan\DiversiTest\PackageManager\PackageManagerConfig;

class PackageManagerFactory
{
    /**
     * @param string|array $config
     * @return PackageManager
     * @throws InvalidConfigException
     */
    public function fromConfig($config)
    {
        return new PackageManager($this->createConfig($config));
    }

    public function createConfig($config)
    {
        if (is_string($config)) {
            if ('composer' === $config) {
                return new PackageManagerConfig(
                    'composer require {% for p, v in configuration %}{{ p }}:{{ v }} {% endfor %}',
                    PackageManager::TEMPLATE_TWIG,
                    PackageManager::ITERATE_CONFIGURATION
                );
            }
            if (false !== strpos($config, '$package')) {
                return new PackageManagerConfig(
                    $config,
                    PackageManager::TEMPLATE_SHELL,
                    PackageManager::ITERATE_PACKAGE
                );
            }
        }
        if (is_array($config)) {
            return new PackageManagerConfig(
                $config['command_line'],
                strtoupper($config['template_engine']),
                strtoupper($config['iteration_type'])
            );
        }
        throw new InvalidConfigException('Cannot parse package_manager definition');
    }
}
