<?php
namespace Ob_Ivan\DiversiTest\PackageManager;

use Ob_Ivan\DiversiTest\InvalidConfigException;

class PackageManagerFactory
{
    const TEMPLATE_SHELL = 'SHELL';
    const TEMPLATE_TWIG = 'TWIG';
    const ITERATE_PACKAGE = 'PACKAGE';
    const ITERATE_CONFIGURATION = 'CONFIGURATION';

    /**
     * @param string|array $configStringOrArray
     * @return PackageManagerInterface
     * @throws InvalidConfigException
     */
    public function fromConfig($configStringOrArray)
    {
        $configObject = $this->createConfig($configStringOrArray);

        if (
            ($configObject->getIterationType() === self::ITERATE_PACKAGE) &&
            ($configObject->getTemplateEngine() === self::TEMPLATE_SHELL)
        ) {
            return new PackageShellPackageManager($configObject->getCommandLine());
        }

        if (
            ($configObject->getIterationType() === self::ITERATE_CONFIGURATION) &&
            ($configObject->getTemplateEngine() === self::TEMPLATE_TWIG)
        ) {
            return new ConfigurationTwigPackageManager($configObject->getCommandLine());
        }

        throw new InvalidConfigException('Unsupported package_manager definition');
    }

    /**
     * @param array|string $config
     * @return PackageManagerConfig
     * @throws InvalidConfigException
     */
    public function createConfig($config)
    {
        if (is_string($config)) {
            if ('composer' === $config) {
                return new PackageManagerConfig(
                    'composer require -W {% for p, v in configuration %}{{ p }}:{{ v }} {% endfor %}',
                    self::TEMPLATE_TWIG,
                    self::ITERATE_CONFIGURATION
                );
            }
            if (false !== strpos($config, '$package')) {
                return new PackageManagerConfig(
                    $config,
                    self::TEMPLATE_SHELL,
                    self::ITERATE_PACKAGE
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
