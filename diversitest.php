<?php
function locateFiles() {
    $dirname = __DIR__;
    for ($i = 0; $i < 10; ++$i) {
        $autoload = "$dirname/vendor/autoload.php";
        $configFilePath = "$dirname/diversitest.yaml";
        if (file_exists($autoload) && file_exists($configFilePath)) {
            return [$autoload, $configFilePath];
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
list($autoload, $configFilePath) = $files;
require_once $autoload;

use Ob_Ivan\DiversiTest\Command\DiversiTestCommand;
use Symfony\Component\Console\Application;

try {
    $application = new Application();
    $command = new DiversiTestCommand($configFilePath);
    $application->add($command);
    $application->setDefaultCommand($command->getName(), true);
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
