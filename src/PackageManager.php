<?php
namespace Ob_Ivan\DiversiTest;

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
            return new static(
                $config,
                self::TEMPLATE_SHELL,
                self::ITERATE_PACKAGE
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
        if ($this->iterationType === self::ITERATE_PACKAGE) {
            $commands = [];
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
    }
}
