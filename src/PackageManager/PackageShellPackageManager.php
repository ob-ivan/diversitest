<?php
namespace Ob_Ivan\DiversiTest\PackageManager;

class PackageShellPackageManager implements PackageManagerInterface
{
    /**
     * @type PackageManagerConfig
     */
    protected $config;

    /**
     * PackageManager constructor.
     *
     * @param PackageManagerConfig $config
     */
    public function __construct(PackageManagerConfig $config)
    {
        $this->config = $config;
    }

    public function getCommands(array $configuration)
    {
        /// @todo Implement getCommands() method.
    }
}
