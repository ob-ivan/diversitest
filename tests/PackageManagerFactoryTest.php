<?php
namespace tests;

use Ob_Ivan\DiversiTest\InvalidConfigException;
use Ob_Ivan\DiversiTest\PackageManager;
use PHPUnit\Framework\TestCase;

class PackageManagerFactoryTest extends TestCase
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
        $packageManager = PackageManagerFactorySpy::fromConfig($config);
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
}
