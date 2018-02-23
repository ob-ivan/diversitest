<?php
namespace tests;

use Ob_Ivan\DiversiTest\PackageManager;

class PackageManagerSpy extends PackageManager
{
    public function getCommandLine(): string
    {
        return $this->commandLine;
    }

    public function getTemplateEngine()
    {
        return $this->templateEngine;
    }

    public function getIterationType()
    {
        return $this->iterationType;
    }
}
