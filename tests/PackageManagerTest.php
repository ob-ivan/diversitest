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
            [
                'config' => [
                    'command_line' => 'hello world',
                    'template_engine' => 'twig',
                    'iteration_type' => 'configuration'
                ],
                'expectedCommandLine' => 'hello world',
                'expectedTemplateEngine' => PackageManager::TEMPLATE_TWIG,
                'expectedIterationType' => PackageManager::ITERATE_CONFIGURATION,
            ],
        ];
    }

    /**
     * @dataProvider provideGetCommands
     */
    public function testGetCommands(
        PackageManager $packageManager,
        array $configuration,
        array $expectedCommands,
        string $message
    ) {
        $actualCommands = $packageManager->getCommands($configuration);
        $this->assertEquals($expectedCommands, $actualCommands, $message);
    }

    public function provideGetCommands()
    {
        return [
            [
                'packageManager' => new PackageManager(
                    'echo $package $version',
                    PackageManager::TEMPLATE_SHELL,
                    PackageManager::ITERATE_PACKAGE
                ),
                'configuration' => [
                    'alice' => 1,
                    'bob' => 3,
                ],
                'expectedCommands' => [
                    'echo alice 1',
                    'echo bob 3',
                ],
                'message' => 'MUST work with shell substitution',
            ],
        ];
    }
}
