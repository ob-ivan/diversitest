<?php
namespace tests;

use Ob_Ivan\DiversiTest\PackageManager;
use PHPUnit\Framework\TestCase;

class PackageManagerTest extends TestCase
{
    public function testGetCommands()
    {
        $packageManager = new PackageManager(
            'echo $package $version',
            PackageManager::ENGINE_SHELL,
            PackageManager::ITERATION_PACKAGE
        );
        $configuration = [
            'alice' => 1,
            'bob' => 3,
        ];
        $expectedCommands = [
            'echo alice 1',
            'echo bob 3',
        ];
        $commands = $packageManager->getCommands($configuration);
        $this->assertEquals($expectedCommands, $commands);
    }
}
