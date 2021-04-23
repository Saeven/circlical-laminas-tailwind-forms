<?php

use Laminas\Router;

return [
    'modules' => [
        'Laminas\Form',
        'Laminas\Router',
        'Circlical\TailwindForms',
    ],
    'module_listener_options' => [
        'config_glob_paths' => [__DIR__ . '/module-config.php'],
        'module_paths' => ['vendor'],
    ],
    'factories' => [
        'Router' => Router\RouterFactory::class,
    ],
];