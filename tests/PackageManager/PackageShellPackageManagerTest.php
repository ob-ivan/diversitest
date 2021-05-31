<?php
namespace tests\PackageManager;

use Ob_Ivan\DiversiTest\InvalidConfigException;
use Ob_Ivan\DiversiTest\PackageManager\PackageManagerConfig;
use Ob_Ivan\DiversiTest\PackageManager\PackageManagerInterface;
use Ob_Ivan\DiversiTest\PackageManager\PackageShellPackageManager;
use PHPUnit\Framework\TestCase;

class PackageShellPackageManagerTest extends TestCase
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
            new PackageShellPackageManager(
                new PackageManagerConfig(
                    $commandLineString,
                    PackageManagerInterface::TEMPLATE_SHELL,
                    PackageManagerInterface::ITERATE_PACKAGE
                )
            )
        ;
        $actualCommands = $packageManager->getCommands($configuration);
        $this->assertEquals($expectedCommands, $actualCommands, $message);
    }

    public function provider_getCommands()
    {
        return [
            [
                'commandLineString' => 'echo $package $version',
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
