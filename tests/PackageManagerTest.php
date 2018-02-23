<?php
namespace tests;

use Ob_Ivan\DiversiTest\PackageManager;
use PHPUnit\Framework\TestCase;

class PackageManagerTest extends TestCase
{
    /**
     * @dataProvider provideFromConfig
     */
    public function testFromConfig(
        $config,
        string $expectedCommandLine,
        $expectedTemplateEngine,
        $expectedIterationType
    ) {
        $packageManager = PackageManagerSpy::fromConfig($config);
        $this->assertEquals(
            $expectedCommandLine,
            $packageManager->getCommandLine(),
            'Command line MUST match'
        );
        $this->assertEquals(
            $expectedTemplateEngine,
            $packageManager->getTemplateEngine(),
            'Template engine MUST match'
        );
        $this->assertEquals(
            $expectedIterationType,
            $packageManager->getIterationType(),
            'Iteration type MUST match'
        );
    }

    public function provideFromConfig()
    {
        return [
            [
                'config' => 'echo $package $version',
                'expectedCommandLine' => 'echo $package $version',
                'expectedTemplateEngine' => PackageManager::TEMPLATE_SHELL,
                'expectedIterationType' => PackageManager::ITERATE_PACKAGE,
            ],
        ];
    }

    public function testGetCommands()
    {
        $packageManager = new PackageManager(
            'echo $package $version',
            PackageManager::TEMPLATE_SHELL,
            PackageManager::ITERATE_PACKAGE
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
