<?php
namespace Ob_Ivan\DiversiTest\PackageManager;

use Ob_Ivan\DiversiTest\InvalidConfigException;

class ConfigurationTwigPackageManager implements PackageManagerInterface
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
            ($config->getIterationType() !== self::ITERATE_CONFIGURATION) ||
            ($config->getTemplateEngine() !== self::TEMPLATE_TWIG)
        ) {
            throw new InvalidConfigException('This class only supports package-shell configuration');
        }

        $this->config = $config;
    }

    public function getCommands(array $configuration)
    {
        /// @todo Implement this method!
    }
}
