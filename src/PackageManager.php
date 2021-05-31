<?php
namespace Ob_Ivan\DiversiTest;

use Twig_Environment;
use Twig_Loader_Array;

class PackageManager
{
    const TEMPLATE_SHELL = 'SHELL';
    const TEMPLATE_TWIG = 'TWIG';
    const ITERATE_PACKAGE = 'PACKAGE';
    const ITERATE_CONFIGURATION = 'CONFIGURATION';

    protected $commandLine;
    protected $templateEngine;
    protected $iterationType;

    /**
     * PackageManager constructor.
     *
     * @param string $commandLine
     * @param string $templateEngine
     * @param string $iterationType
     */
    public function __construct($commandLine, $templateEngine, $iterationType)
    {
        $this->commandLine = $commandLine;
        $this->templateEngine = $templateEngine;
        $this->iterationType = $iterationType;
    }


    /**
     * @param array $configuration
     * @return array
     * @throws InvalidConfigException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getCommands(array $configuration)
    {
        $commands = [];
        if ($this->iterationType === self::ITERATE_PACKAGE) {
            foreach ($configuration as $package => $version) {
                if ($this->templateEngine === self::TEMPLATE_SHELL) {
                    $command = str_replace(
                        ['$package', '$version'],
                        [$package, $version],
                        $this->commandLine
                    );
                }
                $commands[] = $command;
            }
            return $commands;
        }
        if ($this->iterationType === self::ITERATE_CONFIGURATION) {
            if ($this->templateEngine === self::TEMPLATE_TWIG) {
                $templateName = 'command_line';
                $loader = new Twig_Loader_Array([
                    $templateName => $this->commandLine,
                ]);
                $twig = new Twig_Environment($loader);
                $commands[] = trim($twig->render(
                    $templateName,
                    ['configuration' => $configuration]
                ));
                return $commands;
            }
        }
        throw new InvalidConfigException('Unsupported package_manager definition');
    }
}
