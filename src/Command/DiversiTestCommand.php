<?php
namespace Ob_Ivan\DiversiTest\Command;

use Exception;
use Ob_Ivan\DiversiTest\ConfigurationLister;
use Ob_Ivan\DiversiTest\InvalidConfigException;
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
     * @type string
     */
    private $configFilePath;


    /**
     * DiversiTestCommand constructor.
     *
     * @param string $configFilePath
     */
    public function __construct($configFilePath)
    {
        parent::__construct();
        $this->configFilePath = $configFilePath;
    }

    protected function configure()
    {
        $this
            ->setName('diversitest')
            ->setDescription('Runs your tests against varying dependencies versions.')
        ;
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            if (!is_file($this->configFilePath)) {
                throw new InvalidConfigException('Config file does not exist: ' . $this->configFilePath);
            }
            $diversitestConfig = Yaml::parse(file_get_contents($this->configFilePath));
            $packageManagerFactory = new PackageManagerFactory();
            $packageManager = $packageManagerFactory->fromConfig(
                $diversitestConfig['package_manager']
            );
            foreach ($this->getConfigurations($diversitestConfig) as $configuration) {
                $output->writeln('Installing packages: ' . $this->makeConfigurationString($configuration));
                if ($this->install($packageManager, $configuration, $output)) {
                    $output->writeln('Running tests.');
                    $this->runCommand($diversitestConfig['test_runner'], $output);
                } else {
                    $output->writeln('Installation failed, skipping tests.');
                }
            }
        } catch (Exception $e) {
            $output->writeln($this->getName() . ' failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }


    /**
     * @param array $diversitestConfig
     * @return array
     * @throws InvalidConfigException
     */
    private function getConfigurations(array $diversitestConfig)
    {
        $hasConfigurations = isset($diversitestConfig['configurations']);
        $hasPackages = isset($diversitestConfig['packages']);
        if ($hasConfigurations && $hasPackages) {
            throw new InvalidConfigException(
                'MUST NOT provide both "configurations" and "packages" keys.'
            );
        }
        if ($hasConfigurations) {
            return $diversitestConfig['configurations'];
        }
        if ($hasPackages) {
            $lister = new ConfigurationLister();
            return $lister->getConfigurations($diversitestConfig['packages']);
        }
        throw new InvalidConfigException(
            'MUST provide one of "configurations" or "packages" keys in the config file.'
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
     * @param PackageManagerInterface $packageManager
     * @param array $configuration
     * @param OutputInterface $output
     * @return bool If installation was successful
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    private function install(PackageManagerInterface $packageManager, array $configuration, OutputInterface $output)
    {
        foreach ($packageManager->getCommands($configuration) as $command) {
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
        if (method_exists(Process::class, 'fromShellCommandline')) {
            $process = Process::fromShellCommandline($command);
        } else {
            $process = new Process($command);
        }
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });
        return $process->isSuccessful();
    }
}
