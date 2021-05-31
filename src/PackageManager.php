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

    /**
     * @type PackageManagerConfig
     */
    protected $config;

    /**
     * PackageManager constructor.
     *
     * @param string $commandLine
     * @param string $templateEngine
     * @param string $iterationType
     */
    public function __construct($commandLine, $templateEngine, $iterationType)
    {
        $this->config = new PackageManagerConfig($commandLine, $templateEngine, $iterationType);
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
        if ($this->config->getIterationType() === self::ITERATE_PACKAGE) {
            foreach ($configuration as $package => $version) {
                if ($this->config->getTemplateEngine() === self::TEMPLATE_SHELL) {
                    $command = str_replace(
                        ['$package', '$version'],
                        [$package, $version],
                        $this->config->getCommandLine()
                    );
                }
                $commands[] = $command;
            }
            return $commands;
        }
        if ($this->config->getIterationType() === self::ITERATE_CONFIGURATION) {
            if ($this->config->getTemplateEngine() === self::TEMPLATE_TWIG) {
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
        throw new InvalidConfigException('Unsupported package_manager definition');
    }
}
