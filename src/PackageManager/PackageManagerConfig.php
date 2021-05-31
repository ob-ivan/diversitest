<?php
namespace Ob_Ivan\DiversiTest\PackageManager;

class PackageManagerConfig
{
    protected $commandLine;
    protected $templateEngine;
    protected $iterationType;

    /**
     * @param string $commandLine
     * @param string $templateEngine
     * @param string $iterationType
     */
    public function __construct($commandLine, $templateEngine, $iterationType)
    {
        $this->commandLine = $commandLine;
        $this->templateEngine = $templateEngine;
        $this->iterationType = $iterationType;
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
