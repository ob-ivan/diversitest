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
        $output->getFormatter()->setStyle('run', new OutputFormatterStyle('yellow', 'black'));
        $output->getFormatter()->setStyle('run-bold', new OutputFormatterStyle('yellow', 'black', ['bold']));

        $output->writeln('<run>Running diversitest from the project root <run-bold>' . $this->projectRootPath . '</run-bold></run>');

        $buildDirectory = \Cs278\Mktemp\temporaryDir('diversitest.XXXXXX');
        $output->writeln('<run>Created a temporary directory <run-bold>' . $buildDirectory . '</run-bold></run>');

        $output->writeln('<run>Setting permissions for the temporary directory.</run>');
        $filesystem->chmod($buildDirectory, 0777);

        $output->writeln('<run>Copying files from <run-bold>' . $this->projectRootPath . '</run-bold> to <run-bold>' . $buildDirectory . '</run-bold>.</run>');
        $filesystem->mirror($this->projectRootPath, $buildDirectory);

        $output->writeln('<run>Changing directory to <run-bold>' . $buildDirectory . '</run-bold></run>');
        chdir($buildDirectory);

        $output->writeln('<run>Running tests.</run>');
        $diversitestCommand = $this->getApplication()->find('diversitest');
        try {
            $returnCode = $diversitestCommand->run(new ArrayInput([]), $output);
        } catch (Exception $e) {
            $output->writeln('<run>' . $this->getName() . ' failed: ' . $e->getMessage() . '</run>');
            $returnCode = 1;
        }

        $output->writeln('<run>Changing directory back to <run-bold>' . $this->projectRootPath . '</run-bold></run>');
        chdir($this->projectRootPath);

        $output->writeln('<run>Cleaning up temporary directory <run-bold>' . $buildDirectory . '</run-bold></run>');
        $filesystem->remove($buildDirectory);

        $output->writeln('<run>Done.</run>');
        return $returnCode;
    }
}
