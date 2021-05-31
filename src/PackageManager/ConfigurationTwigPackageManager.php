<?php
namespace Ob_Ivan\DiversiTest\PackageManager;

use Twig_Environment;
use Twig_Loader_Array;

class ConfigurationTwigPackageManager implements PackageManagerInterface
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
