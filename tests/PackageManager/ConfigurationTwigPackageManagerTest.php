<?php
namespace tests\PackageManager;

use Ob_Ivan\DiversiTest\InvalidConfigException;
use Ob_Ivan\DiversiTest\PackageManager\ConfigurationTwigPackageManager;
use PHPUnit\Framework\TestCase;

class ConfigurationTwigPackageManagerTest extends TestCase
{
    /**
     * @param string $commandLineString
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
        $commandLineString,
        array $configuration,
        array $expectedCommands,
        $message
    ) {
        $packageManager =
            new ConfigurationTwigPackageManager(
                $commandLineString
            )
        ;
        $actualCommands = $packageManager->getCommands($configuration);
        $this->assertEquals($expectedCommands, $actualCommands, $message);
    }

    public function provider_getCommands()
    {
        return [
            [
                'commandLineString' => 'echo {% for p, v in configuration %}{{ p }}:{{ v }} {% endfor %}',
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
