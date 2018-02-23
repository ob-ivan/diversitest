<?php
namespace tests;

use Ob_Ivan\DiversiTest\DiversiTestCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class DiversiTestCommandTest extends TestCase
{
    public function testExecute()
    {
        $application = new Application();
        $command = new DiversiTestCommand(__DIR__ . '/diversitest-execute.yaml');
        $application->add($command);
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);
        $display = $commandTester->getDisplay();
        $this->assertContains('alice:1 bob:3', $display);
        $this->assertContains('alice:1 bob:4', $display);
        $this->assertContains('alice:2 bob:3', $display);
        $this->assertContains('alice:2 bob:4', $display);
    }
}
