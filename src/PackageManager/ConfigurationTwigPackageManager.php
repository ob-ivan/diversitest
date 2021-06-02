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

    /**
     * @param array $configuration
     * @return string[]
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getCommands(array $configuration)
    {
        /** @type string[] $commands */
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
