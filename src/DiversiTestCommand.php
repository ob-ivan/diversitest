<?php
namespace Ob_Ivan\DiversiTest;

use Ob_Ivan\DiversiTest\RequirementLister;
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

    public function __construct(string $configFilePath)
    {
        parent::__construct();
        $this->config = Yaml::parseFile($configFilePath);
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
        $lister = new RequirementLister();
        foreach ($lister->getRequirements($this->config['packages']) as $requirement) {
            $output->writeln(
                'Installing packages: ' .
                $this->makeRequirementString($requirement)
            );
            $this->install($requirement, $output);
            $output->writeln('Running tests');
            $output->writeln($this->runCommand($this->config['test_runner']));
        }
    }

    private function makeRequirementString(array $requirement): string
    {
        $stringParts = [];
        foreach ($requirement as $package => $version) {
            $stringParts[] = "$package:$version";
        }
        return implode(' ', $stringParts);
    }

    private function install(array $requirement, OutputInterface $output)
    {
        foreach ($requirement as $package => $version) {
            $command = str_replace(
                ['$package', '$version'],
                [$package, $version],
                $this->config['package_manager']
            );
            $output->writeln($this->runCommand($command));
        }
    }

    private function runCommand(string $command): string
    {
        $process = new Process($command);
        $process->run();
        return $process->getOutput();
    }
}
