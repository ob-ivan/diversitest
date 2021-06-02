<?php
namespace tests;

use Ob_Ivan\DiversiTest\DiversiTestCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class DiversiTestCommandTest extends TestCase
{
    /**
     * @param string $configFilePath
     * @param string $expectedOutputFilePath
     * @dataProvider provider_execute
     */
    public function test_execute($configFilePath, $expectedOutputFilePath)
    {
        $application = new Application();
        $command = new DiversiTestCommand($configFilePath);
        $application->add($command);
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);
        $display = $commandTester->getDisplay();
        $this->assertStringEqualsFile($expectedOutputFilePath, $display);
    }

    public function provider_execute()
    {
        return [
            'configurations'             => [
                'configFilePath'         => __DIR__ . '/DiversiTestCommandTest/diversitest-configurations.input.yaml',
                'expectedOutputFilePath' => __DIR__ . '/DiversiTestCommandTest/diversitest-configurations.output.txt',
            ],
            'configuration-and-packages' => [
                'configFilePath'         => __DIR__ . '/DiversiTestCommandTest/diversitest-configurations-and-packages.input.yaml',
                'expectedOutputFilePath' => __DIR__ . '/DiversiTestCommandTest/diversitest-configurations-and-packages.output.txt',
            ],
            'non-existent' => [
                'configFilePath'         => __DIR__ . '/DiversiTestCommandTest/diversitest-non-existent.input.yaml',
                'expectedOutputFilePath' => __DIR__ . '/DiversiTestCommandTest/diversitest-non-existent.output.txt',
            ],
            'packages'                   => [
                'configFilePath'         => __DIR__ . '/DiversiTestCommandTest/diversitest-packages.input.yaml',
                'expectedOutputFilePath' => __DIR__ . '/DiversiTestCommandTest/diversitest-packages.output.txt',
            ],
            'twig'                       => [
                'configFilePath'         => __DIR__ . '/DiversiTestCommandTest/diversitest-twig.input.yaml',
                'expectedOutputFilePath' => __DIR__ . '/DiversiTestCommandTest/diversitest-twig.output.txt',
            ],
        ];
    }
}
