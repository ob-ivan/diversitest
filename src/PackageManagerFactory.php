<?php
namespace Ob_Ivan\DiversiTest;

class PackageManagerFactory
{
    /**
     * @param string|array $config
     * @return PackageManager
     * @throws InvalidConfigException
     */
    public function fromConfig($config)
    {
        if (is_string($config)) {
            if ('composer' === $config) {
                return $this->createInstance(
                    'composer require {% for p, v in configuration %}{{ p }}:{{ v }} {% endfor %}',
                    PackageManager::TEMPLATE_TWIG,
                    PackageManager::ITERATE_CONFIGURATION
                );
            }
            if (false !== strpos($config, '$package')) {
                return $this->createInstance(
                    $config,
                    PackageManager::TEMPLATE_SHELL,
                    PackageManager::ITERATE_PACKAGE
                );
            }
        }
        if (is_array($config)) {
            return $this->createInstance(
                $config['command_line'],
                strtoupper($config['template_engine']),
                strtoupper($config['iteration_type'])
            );
        }
        throw new InvalidConfigException('Cannot parse package_manager definition');
    }


    protected function createInstance($commandLine, $templateEngine, $iterationType)
    {
        return new PackageManager($commandLine, $templateEngine, $iterationType);
    }
}
