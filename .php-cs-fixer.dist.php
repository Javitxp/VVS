<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__ . '/app')
    ->exclude('vendor');

$config = new Config();
$config->setRules([
    '@PSR12' => true,
]);
$config->setFinder($finder);

return $config;
