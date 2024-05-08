<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__ . '/app') // Buscar en el directorio 'app' en la raíz de tu proyecto
    ->exclude('vendor');

$config = new Config();
$config->setRules([
    'class_name' => [
        'format' => 'camel_case', // Esto especifica que los nombres de las clases deben estar en CamelCase
    ],
]);
$config->setFinder($finder);

return $config;
