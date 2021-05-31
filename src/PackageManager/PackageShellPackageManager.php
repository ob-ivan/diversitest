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
