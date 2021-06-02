<?php
namespace tests;

use Ob_Ivan\DiversiTest\DiversiTestCommand;
use Ob_Ivan\DiversiTest\InvalidConfigException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class DiversiTestCommandTest extends TestCase
{
    /**
     * @param string $filename
     * @param string $expectedLines
     * @throws InvalidConfigException
     * @dataProvider provider_execute
     */
    public function test_execute($filename, $expectedLines)
    {
        $application = new Application();
        $command = new DiversiTestCommand($filename);
        $application->add($command);
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);
        $display = $commandTester->getDisplay();
        $this->assertStringEqualsFile($expectedLines, $display);
    }

    public function provider_execute()
    {
        return [
            'configurations' => [
                'filename' => __DIR__ . '/DiversiTestCommandTest/diversitest-configurations.input.yaml',
                'expectedLines' => __DIR__ . '/DiversiTestCommandTest/diversitest-configurations.output.txt',
            ],
            'packages' => [
                'filename' => __DIR__ . '/DiversiTestCommandTest/diversitest-packages.input.yaml',
                'expectedLines' => __DIR__ . '/DiversiTestCommandTest/diversitest-packages.output.txt',
            ],
            'twig' => [
                'filename' => __DIR__ . '/DiversiTestCommandTest/diversitest-twig.input.yaml',
                'expectedLines' => __DIR__ . '/DiversiTestCommandTest/diversitest-twig.output.txt',
            ],
        ];
    }
}
