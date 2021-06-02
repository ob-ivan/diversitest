<?php
namespace Ob_Ivan\DiversiTest\PackageManager;

interface PackageManagerInterface
{
    /**
     * @param array $configuration
     * @return string[]
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getCommands(array $configuration);
}
