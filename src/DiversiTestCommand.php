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
            if ($this->install($requirement, $output)) {
                $output->writeln('Running tests');
                $this->runCommand($this->config['test_runner'], $output);
            } else {
                $output->writeln('Installation failed, skipping tests');
            }
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

    /**
     * @return bool If installation was successful
     */
    private function install(array $requirement, OutputInterface $output): bool
    {
        foreach ($requirement as $package => $version) {
            $command = str_replace(
                ['$package', '$version'],
                [$package, $version],
                $this->config['package_manager']
            );
            if (!$this->runCommand($command, $output)) {
                return false;
            }
        }
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
