<?php
namespace Ob_Ivan\DiversiTest\PackageManager;

use Ob_Ivan\DiversiTest\InvalidConfigException;
use Twig_Environment;
use Twig_Loader_Array;

class ConfigurationTwigPackageManager implements PackageManagerInterface
{
    /**
     * @type string
     */
    protected $commandLineString;

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

        $this->commandLineString = $config->getCommandLine();
    }

    public function getCommands(array $configuration)
    {
        $commands = [];
        $templateName = 'command_line';
        $loader = new Twig_Loader_Array([
            $templateName => $this->commandLineString,
        ]);
        $twig = new Twig_Environment($loader);
        $commands[] = trim($twig->render(
            $templateName,
            ['configuration' => $configuration]
        ));
        return $commands;
    }
}
