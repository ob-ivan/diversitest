<?php
namespace tests;

use Ob_Ivan\DiversiTest\ConfigurationLister;
use PHPUnit\Framework\TestCase;

class ConfigurationListerTest extends TestCase
{
    public function testGetConfigurations()
    {
        $packages = [
            'pi' => [3, 14],
            'e'  => [2, 71],
        ];
        $expected = [
            [
                'pi' => 3,
                'e' => 2,
            ],
            [
                'pi' => 14,
                'e' => 2,
            ],
            [
                'pi' => 3,
                'e' => 71,
            ],
            [
                'pi' => 14,
                'e' => 71,
            ],
        ];
        $lister = new ConfigurationLister();
        $this->assertEquals($expected, $lister->getConfigurations($packages));
    }
}
