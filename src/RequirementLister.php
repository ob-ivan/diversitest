<?php
namespace Ob_Ivan\DiversiTest;

class RequirementLister
{
    public function getRequirements(array $packages): array
    {
        $requirementCount = 1;
        foreach ($packages as $package => $versions) {
            $requirementCount *= count($versions);
        }
        $requirements = [];
        for ($requirementId = 0; $requirementId < $requirementCount; ++$requirementId) {
            $requirement = [];
            $runningId = $requirementId;
            foreach ($packages as $package => $versions) {
                $versionCount = count($versions);
                $remainder = $runningId % $versionCount;
                $runningId -= $remainder;
                $runningId /= $versionCount;
                $requirement[$package] = $versions[$remainder];
            }
            $requirements[] = $requirement;
        }
        return $requirements;
    }
}
