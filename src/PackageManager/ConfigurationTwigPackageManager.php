<?php
namespace Ob_Ivan\DiversiTest\PackageManager;

use Ob_Ivan\DiversiTest\InvalidConfigException;
use Twig_Environment;
use Twig_Loader_Array;

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
        $commands = [];
        $templateName = 'command_line';
        $loader = new Twig_Loader_Array([
            $templateName => $this->config->getCommandLine(),
        ]);
        $twig = new Twig_Environment($loader);
        $commands[] = trim($twig->render(
            $templateName,
            ['configuration' => $configuration]
        ));
        return $commands;
    }
}
