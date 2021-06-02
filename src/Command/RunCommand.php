<?php
namespace Ob_Ivan\DiversiTest\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
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
        $output->getFormatter()->setStyle('color', new OutputFormatterStyle('yellow', 'black'));
        $output->getFormatter()->setStyle('bold', new OutputFormatterStyle('yellow', 'black', ['bold']));

        /*
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"
echo "${COLOR}Running diversitest from directory ${BOLD}${SCRIPT_DIR}${RESET}"

BUILD_DIR="$(mktemp -d)"
echo "${COLOR}Created a temporary directory ${BOLD}${BUILD_DIR}${RESET}"

echo "${COLOR}Setting permissions for the temporary directory${RESET}"
chmod a+rsx "${BUILD_DIR}"

echo "${COLOR}Copying from local directory${RESET}"
cp -R . "${BUILD_DIR}"

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
