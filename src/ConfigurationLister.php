<?php
namespace Ob_Ivan\DiversiTest;

class ConfigurationLister
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
    public function getConfigurations(array $packages): array
    {
        $configurationCount = 1;
        foreach ($packages as $package => $versions) {
            $configurationCount *= count($versions);
        }
        $configurations = [];
        for ($configurationId = 0; $configurationId < $configurationCount; ++$configurationId) {
            $configuration = [];
            $runningId = $configurationId;
            foreach ($packages as $package => $versions) {
                $versionCount = count($versions);
                $remainder = $runningId % $versionCount;
                $runningId -= $remainder;
                $runningId /= $versionCount;
                $configuration[$package] = $versions[$remainder];
            }
            $configurations[] = $configuration;
        }
        return $configurations;
    }
}
