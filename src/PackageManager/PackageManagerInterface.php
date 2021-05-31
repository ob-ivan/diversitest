<?php
namespace Ob_Ivan\DiversiTest\PackageManager;

use Ob_Ivan\DiversiTest\InvalidConfigException;

interface PackageManagerInterface
{
    const TEMPLATE_SHELL = 'SHELL';
    const TEMPLATE_TWIG = 'TWIG';
    const ITERATE_PACKAGE = 'PACKAGE';
    const ITERATE_CONFIGURATION = 'CONFIGURATION';

    /**
     * @param array $configuration
     * @return array
     * @throws InvalidConfigException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getCommands(array $configuration);
}
