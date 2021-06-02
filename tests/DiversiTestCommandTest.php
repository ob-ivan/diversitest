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
     * @param array $expectedLines
     * @throws InvalidConfigException
     * @dataProvider provider_execute
     */
    public function test_execute($filename, array $expectedLines)
    {
        $application = new Application();
        $command = new DiversiTestCommand($filename);
        $application->add($command);
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);
        $display = $commandTester->getDisplay();
        foreach ($expectedLines as $expectedLine) {
            $this->assertContains($expectedLine, $display);
        }
    }

    public function provider_execute()
    {
        return [
            'configurations' => [
                'filename' => __DIR__ . '/DiversiTestCommandTest/diversitest-configurations.yaml',
                'expectedLines' => [
                    'alice:1 bob:3',
                    'alice:1 bob:4',
                    'alice:2 bob:3',
                ],
            ],
            'packages' => [
                'filename' => __DIR__ . '/DiversiTestCommandTest/diversitest-packages.yaml',
                'expectedLines' => [
                    'alice:1 bob:3',
                    'alice:1 bob:4',
                    'alice:2 bob:3',
                    'alice:2 bob:4',
                ],
            ],
            'twig' => [
                'filename' => __DIR__ . '/DiversiTestCommandTest/diversitest-twig.yaml',
                'expectedLines' => [
                    'installing alice:1 bob:3',
                    'installing alice:1 bob:4',
                    'installing alice:2 bob:3',
                ],
            ],
        ];
    }
}
