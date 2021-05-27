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
     * @dataProvider provideExecute
     * @throws InvalidConfigException
     */
    public function testExecute($filename, array $expectedLines)
    {
        $application = new Application();
        $command = new DiversiTestCommand(__DIR__ . '/' . $filename);
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

    public function provideExecute()
    {
        return [
            [
                'filename' => 'diversitest-packages.yaml',
                'expectedLines' => [
                    'alice:1 bob:3',
                    'alice:1 bob:4',
                    'alice:2 bob:3',
                    'alice:2 bob:4',
                ],
            ],
            [
                'filename' => 'diversitest-configurations.yaml',
                'expectedLines' => [
                    'alice:1 bob:3',
                    'alice:1 bob:4',
                    'alice:2 bob:3',
                ],
            ],
            [
                'filename' => 'diversitest-twig.yaml',
                'expectedLines' => [
                    'installing alice:1 bob:3',
                    'installing alice:1 bob:4',
                    'installing alice:2 bob:3',
                ],
            ],
        ];
    }
}
