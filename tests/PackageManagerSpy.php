<?php
namespace tests;

use Ob_Ivan\DiversiTest\PackageManager;

class PackageManagerSpy extends PackageManager
{
    public static function createInstance($commandLine, $templateEngine, $iterationType)
    {
        return new PackageManagerSpy($commandLine, $templateEngine, $iterationType);
    }

    /**
     * @return string
     */
    public function getCommandLine()
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
