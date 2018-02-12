<?php
namespace Ob_Ivan\DiversiTest;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class DiversiTestCommand extends Command
{
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
        // ...
    }
}
