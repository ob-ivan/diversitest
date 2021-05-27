<?php
namespace tests;

use Ob_Ivan\DiversiTest\InvalidConfigException;
use Ob_Ivan\DiversiTest\PackageManager;
use PHPUnit\Framework\TestCase;

class PackageManagerTest extends TestCase
{
    /**
     * @param array|string $config
     * @param string $expectedCommandLine
     * @param string expectedTemplateEngine
     * @param string $expectedIterationType
     * @throws InvalidConfigException
     * @dataProvider provider_fromConfig
     */
    public function test_fromConfig(
        $config,
        $expectedCommandLine,
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

    public function provider_fromConfig()
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
            [
                'config' => 'composer',
                'expectedCommandLine' => 'composer require {% for p, v in configuration %}{{ p }}:{{ v }} {% endfor %}',
                'expectedTemplateEngine' => PackageManager::TEMPLATE_TWIG,
                'expectedIterationType' => PackageManager::ITERATE_CONFIGURATION,
            ],
        ];
    }


    /**
     * @param PackageManager $packageManager
     * @param array $configuration
     * @param array $expectedCommands
     * @param string $message
     * @throws InvalidConfigException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @dataProvider provider_getCommands
     */
    public function test_getCommands(
        PackageManager $packageManager,
        array $configuration,
        array $expectedCommands,
        $message
    ) {
        $actualCommands = $packageManager->getCommands($configuration);
        $this->assertEquals($expectedCommands, $actualCommands, $message);
    }

    public function provider_getCommands()
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
            [
                'packageManager' => new PackageManager(
                    'echo {% for p, v in configuration %}{{ p }}:{{ v }} {% endfor %}',
                    PackageManager::TEMPLATE_TWIG,
                    PackageManager::ITERATE_CONFIGURATION
                ),
                'configuration' => [
                    'alice' => 1,
                    'bob' => 3,
                ],
                'expectedCommands' => [
                    'echo alice:1 bob:3',
                ],
                'message' => 'MUST work with twig while iterating over configurations',
            ],
        ];
    }
}
