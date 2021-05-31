<?php
namespace tests\PackageManager;

use Ob_Ivan\DiversiTest\InvalidConfigException;
use Ob_Ivan\DiversiTest\PackageManager\ConfigurationTwigPackageManager;
use Ob_Ivan\DiversiTest\PackageManager\PackageManagerConfig;
use Ob_Ivan\DiversiTest\PackageManager\PackageManagerInterface;
use Ob_Ivan\DiversiTest\PackageManager\PackageShellPackageManager;
use PHPUnit\Framework\TestCase;

class PackageManagerTest extends TestCase
{
    /**
     * @param PackageManagerInterface $packageManager
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
        PackageManagerInterface $packageManager,
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
                'packageManager' => new PackageShellPackageManager(
                    new PackageManagerConfig(
                        'echo $package $version',
                        PackageManagerInterface::TEMPLATE_SHELL,
                        PackageManagerInterface::ITERATE_PACKAGE
                    )
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
                'packageManager' => new ConfigurationTwigPackageManager(
                    new PackageManagerConfig(
                        'echo {% for p, v in configuration %}{{ p }}:{{ v }} {% endfor %}',
                        PackageManagerInterface::TEMPLATE_TWIG,
                        PackageManagerInterface::ITERATE_CONFIGURATION
                    )
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
