<?php
namespace Ob_Ivan\DiversiTest\PackageManager;

use Ob_Ivan\DiversiTest\InvalidConfigException;
use Ob_Ivan\DiversiTest\PackageManager\PackageManagerConfig;
use Twig_Environment;
use Twig_Loader_Array;

class PackageManager implements PackageManagerInterface
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
