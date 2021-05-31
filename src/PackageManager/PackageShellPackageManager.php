<?php
namespace Ob_Ivan\DiversiTest\PackageManager;

use Ob_Ivan\DiversiTest\InvalidConfigException;

class PackageShellPackageManager implements PackageManagerInterface
{
    /**
     * @type PackageManagerConfig
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param PackageManagerConfig $config
     *
     * @throws InvalidConfigException
     */
    public function __construct(PackageManagerConfig $config)
    {
        if (
            ($config->getIterationType() !== self::ITERATE_PACKAGE) ||
            ($config->getTemplateEngine() !== self::TEMPLATE_SHELL)
        ) {
            throw new InvalidConfigException('This class only supports package-shell configuration');
        }

        $this->config = $config;
    }

    public function getCommands(array $configuration)
    {
        foreach ($configuration as $package => $version) {
            $command = str_replace(
                ['$package', '$version'],
                [$package, $version],
                $this->config->getCommandLine()
            );
            $commands[] = $command;
        }
        return $commands;
    }
}
