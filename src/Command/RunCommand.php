<?php
namespace Ob_Ivan\DiversiTest\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
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

        /*
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"
echo "${COLOR}Running diversitest from directory ${BOLD}${SCRIPT_DIR}${RESET}"
        */

        $buildDirectory = \Cs278\Mktemp\temporaryDir('diversitest.XXXXXX');
        $output->writeln('<color>Created a temporary directory <bold>' . $buildDirectory . '</bold></color>');

        $output->writeln('<color>Setting permissions for the temporary directory.</color>');
        $filesystem->chmod($buildDirectory, 0777);

        $output->writeln('<color>Copying from local directory$.</color>');
        $filesystem->mirror($this->projectRootPath, $buildDirectory);

        /*
echo "${COLOR}Changing dir to ${BOLD}${BUILD_DIR}${RESET}"
cd "${BUILD_DIR}"

echo "${COLOR}Running tests${RESET}"
"${PHP_COMMAND}" "${SCRIPT_DIR}/diversitest.php"

echo "${COLOR}Cleaning up temporary dir ${BOLD}${BUILD_DIR}${RESET}"
rm -rf "${BUILD_DIR}"

echo "${COLOR}Done${RESET}"
exit 0
         */
    }
}
