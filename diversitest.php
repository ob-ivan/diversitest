<?php
function locateFiles() {
    $dirname = __DIR__;
    for ($i = 0; $i < 10; ++$i) {
        $autoload = "$dirname/vendor/autoload.php";
        $config = "$dirname/diversitest.yaml";
        if (file_exists($autoload) && file_exists($config)) {
            return [$autoload, $config];
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
list($autoload, $config) = $files;
require_once $autoload;

use Ob_Ivan\DiversiTest\DiversiTestCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$command = new DiversiTestCommand($config);
$application->add($command);
$application->setDefaultCommand($command->getName(), true);
$application->run();
