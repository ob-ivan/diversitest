<?php
function locateFiles() {
    $dirname = __DIR__;
    for ($i = 0; $i < 10; ++$i) {
        $autoloadFilePath = "$dirname/vendor/autoload.php";
        $configFilePath = "$dirname/diversitest.yaml";
        if (file_exists($autoloadFilePath) && file_exists($configFilePath)) {
            return [$dirname, $autoloadFilePath, $configFilePath];
        }
        $dirname = dirname($dirname);
    }
    return null;
}

$files = locateFiles();
if (!$files) {
    print "Could not locate project root up from " . __DIR__ . "\n";
    exit(1);
}
list($projectRootPath, $autoloadFilePath, $configFilePath) = $files;

require_once $autoloadFilePath;

use Ob_Ivan\DiversiTest\Command\DiversiTestCommand;
use Ob_Ivan\DiversiTest\Command\RunCommand;
use Symfony\Component\Console\Application;

try {
    $application = new Application();
    $diversitestCommand = new DiversiTestCommand($configFilePath);
    $runCommand = new RunCommand($projectRootPath);
    $application->add($diversitestCommand);
    $application->add($runCommand);
    $application->setDefaultCommand($diversitestCommand->getName(), true);
    $application->run();
}
catch (Exception $e) {
    do {
        echo 'Exception ' . get_class($e) . ' (' . $e->getCode() . '): "' . $e->getMessage() . '"' . PHP_EOL;
        echo 'Thrown in ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
        echo 'Stack trace: ' . $e->getTraceAsString() . PHP_EOL;
    }
    while ($e = $e->getPrevious());
}
