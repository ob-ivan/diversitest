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

    public static function fromConfig($config): self
    {
        if (is_string($config)) {
            if ('composer' === $config) {
                return new static(
                    'composer require {% for p, v in configuration %}{{ p }}:{{ v }} {% endfor %}',
                    self::TEMPLATE_TWIG,
                    self::ITERATE_CONFIGURATION
                );
            }
            if (false !== strpos($config, '$package')) {
                return new static(
                    $config,
                    self::TEMPLATE_SHELL,
                    self::ITERATE_PACKAGE
                );
            }
        }
        if (is_array($config)) {
            return new static(
                $config['command_line'],
                strtoupper($config['template_engine']),
                strtoupper($config['iteration_type'])
            );
        }
        throw new InvalidConfigException('Cannot parse package_manager definition');
    }

    public function __construct(string $commandLine, $templateEngine, $iterationType)
    {
        $this->commandLine = $commandLine;
        $this->templateEngine = $templateEngine;
        $this->iterationType = $iterationType;
    }

    public function getCommands(array $configuration): array
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
