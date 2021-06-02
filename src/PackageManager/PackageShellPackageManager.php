<?php
namespace Ob_Ivan\DiversiTest\PackageManager;

class PackageShellPackageManager implements PackageManagerInterface
{
    /**
     * @type string
     */
    private $commandLineString;

    /**
     * Constructor.
     *
     * @param string $commandLineString
     */
    public function __construct($commandLineString)
    {
        $this->commandLineString = $commandLineString;
    }

    /**
     * @param array $configuration
     * @return string[]
     */
    public function getCommands(array $configuration)
    {
        /** @type string[] $commands */
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
