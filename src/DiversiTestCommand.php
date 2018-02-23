<?php
namespace Ob_Ivan\DiversiTest;

use Ob_Ivan\DiversiTest\ConfigurationLister;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

class DiversiTestCommand extends Command
{
    /**
     * Project configuration values.
     *
     * @var array {
     *      package_manager: string with $package and $version placeholders
     *      test_runner: string
     *      packages: array {
     *          [package: string]: array {
     *              version: string,
     *              ...
     *          },
     *          ...
     *      }
     * }
     */
    private $config;

    /** @var PackageManager */
    private $packageManager;

    public function __construct(string $configFilePath)
    {
        parent::__construct();
        $this->config = Yaml::parseFile($configFilePath);
        $this->packageManager = PackageManager::fromConfig(
            $this->config['package_manager']
        );
    }

    protected function configure()
    {
        $this
            ->setName('diversitest')
            ->setDescription('Runs your tests against varying dependecies versions')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->getConfigurations() as $configuration) {
            $output->writeln(
                'Installing packages: ' .
                $this->makeConfigurationString($configuration)
            );
            if ($this->install($configuration, $output)) {
                $output->writeln('Running tests');
                $this->runCommand($this->config['test_runner'], $output);
            } else {
                $output->writeln('Installation failed, skipping tests');
            }
        }
    }

    private function getConfigurations(): array
    {
        $hasConfigurations = isset($this->config['configurations']);
        $hasPackages = isset($this->config['packages']);
        if ($hasConfigurations && $hasPackages) {
            throw new InvalidConfigException(
                'MUST NOT provide both configurations and packages key'
            );
        }
        if ($hasConfigurations) {
            return $this->config['configurations'];
        }
        if ($hasPackages) {
            $lister = new ConfigurationLister();
            return $lister->getConfigurations($this->config['packages']);
        }
        throw new InvalidConfigException(
            'MUST provide one of configurations or packages keys in config file'
        );
    }

    private function makeConfigurationString(array $configuration): string
    {
        $stringParts = [];
        foreach ($configuration as $package => $version) {
            $stringParts[] = "$package:$version";
        }
        return implode(' ', $stringParts);
    }

    /**
     * @return bool If installation was successful
     */
    private function install(array $configuration, OutputInterface $output): bool
    {
        // NEW
        foreach ($this->packageManager->getCommands($configuration) as $command) {
            if (!$this->runCommand($command, $output)) {
                return false;
            }
        }
        // OLD
        /*
        foreach ($configuration as $package => $version) {
            $command = str_replace(
                ['$package', '$version'],
                [$package, $version],
                $this->config['package_manager']
            );
            if (!$this->runCommand($command, $output)) {
                return false;
            }
        }
        */
        // END
        return true;
    }

    /**
     * @return bool Whether command returned success code
     */
    private function runCommand(string $command, OutputInterface $output): bool
    {
        $process = new Process($command);
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });
        return $process->isSuccessful();
    }
}
