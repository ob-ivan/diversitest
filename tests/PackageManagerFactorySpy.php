<?php
namespace tests;

use Ob_Ivan\DiversiTest\PackageManagerFactory;

class PackageManagerFactorySpy extends PackageManagerFactory
{
    public static function createInstance($commandLine, $templateEngine, $iterationType)
    {
        return new PackageManagerSpy($commandLine, $templateEngine, $iterationType);
    }
}
