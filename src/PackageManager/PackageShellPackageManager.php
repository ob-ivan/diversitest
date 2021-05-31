<?php
namespace Ob_Ivan\DiversiTest\PackageManager;

use Ob_Ivan\DiversiTest\InvalidConfigException;

class PackageShellPackageManager implements PackageManagerInterface
{
    /**
     * @type string
     */
    private $commandLineString;

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

        $this->commandLineString = $config->getCommandLine();
    }

    public function getCommands(array $configuration)
    {
        $commands = [];
        foreach ($configuration as $package => $version) {
            $commands[] = str_replace(
                ['$package', '$version'],
                [$package, $version],
                $this->commandLineString
            );
        }
        return $commands;
    }
}
