<?php
namespace Ob_Ivan\DiversiTest;

class RequirementLister
{
    /**
     * Return all variations of packages' versions.
     *
     * @param array $packages {
     *      [string $package]: string[] $versions
     * }
     * @return array {
     *      array {
     *          [string $package]: string $version,
     *      }
     * }
     */
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
