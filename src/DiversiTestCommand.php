<?php
namespace Ob_Ivan\DiversiTest;

use Ob_Ivan\DiversiTest\RequirementLister;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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

    public function __construct(string $configFilePath) {
        $this->config = Yaml::parseYaml($configFilePath);
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
            $this->install($requirement);
            $output->writeln('Running tests');
            $this->run($this->config['test_runner']);
        }
    }
}
