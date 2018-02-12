<?php
namespace tests;

use Ob_Ivan\DiversiTest\RequirementLister;
use PHPUnit\Framework\TestCase;

class RequirementListerTest extends TestCase
{
    public function testGetRequirements()
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
        $lister = new RequirementLister();
        $this->assertEquals($expected, $lister->getRequirements($packages));
    }
}
