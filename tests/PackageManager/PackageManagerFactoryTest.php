<?php
namespace tests\PackageManager;

use Ob_Ivan\DiversiTest\InvalidConfigException;
use Ob_Ivan\DiversiTest\PackageManager\PackageManagerFactory;
use PHPUnit\Framework\TestCase;

class PackageManagerFactoryTest extends TestCase
{
    /**
     * @param array|string $config
     * @param string $expectedCommandLine
     * @param string expectedTemplateEngine
     * @param string $expectedIterationType
     * @throws InvalidConfigException
     * @dataProvider provider_createConfig
     */
    public function test_createConfig(
        $config,
        $expectedCommandLine,
        $expectedTemplateEngine,
        $expectedIterationType
    ) {
        $packageManagerFactory = new PackageManagerFactory();
        $packageManagerConfig = $packageManagerFactory->createConfig($config);
        $this->assertEquals(
            $expectedCommandLine,
            $packageManagerConfig->getCommandLine(),
            'Command line MUST match'
        );
        $this->assertEquals(
            $expectedTemplateEngine,
            $packageManagerConfig->getTemplateEngine(),
            'Template engine MUST match'
        );
        $this->assertEquals(
            $expectedIterationType,
            $packageManagerConfig->getIterationType(),
            'Iteration type MUST match'
        );
    }

    public function provider_createConfig()
    {
        return [
            [
                'config' => 'echo $package $version',
                'expectedCommandLine' => 'echo $package $version',
                'expectedTemplateEngine' => PackageManagerFactory::TEMPLATE_SHELL,
                'expectedIterationType' => PackageManagerFactory::ITERATE_PACKAGE,
            ],
            [
                'config' => [
                    'command_line' => 'hello world',
                    'template_engine' => 'twig',
                    'iteration_type' => 'configuration'
                ],
                'expectedCommandLine' => 'hello world',
                'expectedTemplateEngine' => PackageManagerFactory::TEMPLATE_TWIG,
                'expectedIterationType' => PackageManagerFactory::ITERATE_CONFIGURATION,
            ],
            [
                'config' => 'composer',
                'expectedCommandLine' => 'composer require {% for p, v in configuration %}{{ p }}:{{ v }} {% endfor %}',
                'expectedTemplateEngine' => PackageManagerFactory::TEMPLATE_TWIG,
                'expectedIterationType' => PackageManagerFactory::ITERATE_CONFIGURATION,
            ],
        ];
    }
}
