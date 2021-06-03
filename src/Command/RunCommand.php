<?php
namespace Ob_Ivan\DiversiTest\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class RunCommand extends Command
{
    /**
     * @type string
     */
    private $projectRootPath;


    /**
     * @param string $projectRootPath
     */
    public function __construct($projectRootPath)
    {
        parent::__construct();

        $this->projectRootPath = $projectRootPath;
    }

    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run the diversitest suite.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filesystem = new Filesystem();
        $output->getFormatter()->setStyle('color', new OutputFormatterStyle('yellow', 'black'));
        $output->getFormatter()->setStyle('bold', new OutputFormatterStyle('yellow', 'black', ['bold']));

        $output->writeln('<color>Running diversitest from the project root <bold>' . $this->projectRootPath . '</bold></color>');

        $buildDirectory = \Cs278\Mktemp\temporaryDir('diversitest.XXXXXX');
        $output->writeln('<color>Created a temporary directory <bold>' . $buildDirectory . '</bold></color>');

        $output->writeln('<color>Setting permissions for the temporary directory.</color>');
        $filesystem->chmod($buildDirectory, 0777);

        $output->writeln('<color>Copying from local directory.</color>');
        $filesystem->mirror($this->projectRootPath, $buildDirectory);

        $output->writeln('<color>Changing directory to <bold>' . $buildDirectory . '</bold></color>');
        chdir($buildDirectory);

        $output->writeln('<color>Running tests.</color>');
        $diversitestCommand = $this->getApplication()->find('diversitest');
        $arguments = [
            'command' => $diversitestCommand->getName(), // TODO: Is it needed?
        ];

        try {
            $returnCode = $diversitestCommand->run(new ArrayInput($arguments), $output);
        } catch (Exception $e) {
            $output->writeln('<color>' . $this->getName() . ' failed: ' . $e->getMessage() . '</color>');
            $returnCode = 1;
        }

        $output->writeln('<color>Cleaning up temporary directory <bold>' . $buildDirectory . '</bold></color>');
        $filesystem->remove($buildDirectory);

        $output->writeln('<color>Done.</color>');
        return $returnCode;
    }
}
