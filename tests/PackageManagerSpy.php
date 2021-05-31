<?php
namespace tests;

use Ob_Ivan\DiversiTest\PackageManager;

class PackageManagerSpy extends PackageManager
{
    /**
     * @return string
     */
    public function getCommandLine()
    {
        return $this->config->getCommandLine();
    }

    public function getTemplateEngine()
    {
        return $this->config->getTemplateEngine();
    }

    public function getIterationType()
    {
        return $this->config->getIterationType();
    }
}
