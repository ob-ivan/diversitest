<?php
namespace Ob_Ivan\DiversiTest;

use Ob_Ivan\DiversiTest\PackageManager\PackageManager;
use Ob_Ivan\DiversiTest\PackageManager\PackageManagerFactory;
use Ob_Ivan\DiversiTest\PackageManager\PackageManagerInterface;
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

    /** @var PackageManagerInterface */
    private $packageManager;


    /**
     * DiversiTestCommand constructor.
     *
     * @param string $configFilePath
     * @throws InvalidConfigException
     */
    public function __construct($configFilePath)
    {
        parent::__construct();
        $this->config = Yaml::parse(file_get_contents($configFilePath));
        $packageManagerFactory = new PackageManagerFactory();
        $this->packageManager = $packageManagerFactory->fromConfig(
            $this->config['package_manager']
        );
    }

    protected function configure()
    {
        $this
            ->setName('diversitest')
            ->setDescription('Runs your tests against varying dependencies versions')
        ;
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws InvalidConfigException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
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

        return 0;
    }


    /**
     * @return array
     * @throws InvalidConfigException
     */
    private function getConfigurations()
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


    /**
     * @param array $configuration
     * @return string
     */
    private function makeConfigurationString(array $configuration)
    {
        $stringParts = [];
        foreach ($configuration as $package => $version) {
            $stringParts[] = "$package:$version";
        }
        return implode(' ', $stringParts);
    }


    /**
     * @param array $configuration
     * @param OutputInterface $output
     * @return bool If installation was successful
     * @throws InvalidConfigException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    private function install(array $configuration, OutputInterface $output)
    {
        foreach ($this->packageManager->getCommands($configuration) as $command) {
            if (!$this->runCommand($command, $output)) {
                return false;
            }
        }
        return true;
    }


    /**
     * @param string $command
     * @param OutputInterface $output
     * @return bool Whether command returned success code
     */
    private function runCommand($command, OutputInterface $output)
    {
        $process = new Process($command);
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });
        return $process->isSuccessful();
    }
}
