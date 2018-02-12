<?php
if (!(function () {
    $dirname = __DIR__;
    for ($i = 0; $i < 10; ++$i) {
        $autoload = "$dirname/vendor/autoload.php";
        if (file_exists($autoload)) {
            require_once $autoload;
            return true;
        }
        $dirname = dirname($dirname);
    }
    return false;
})()) {
    print "Could not find autoloader for " . __DIR__ . "\n";
    exit(1);
}

use Ob_Ivan\DiversiTest\DiversiTestCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$command = new DiversiTestCommand();
$application->add($command);
$application->setDefaultCommand($command->getName(), true);
$application->run();
