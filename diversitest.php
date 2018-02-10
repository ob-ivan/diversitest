<?php
require __DIR__ . '/vendor/autoload.php';

use Ob_Ivan\DiversiTest\DiversiTestCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$command = new DiversiTestCommand();
$application->add($command);
$application->setDefaultCommand($command->getName(), true);
$application->run();
