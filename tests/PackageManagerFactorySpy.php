<?php
namespace tests;

use Ob_Ivan\DiversiTest\PackageManagerConfig;
use Ob_Ivan\DiversiTest\PackageManagerFactory;

class PackageManagerFactorySpy extends PackageManagerFactory
{
    protected function createInstance(PackageManagerConfig $config)
    {
        return new PackageManagerSpy($config);
    }
}
