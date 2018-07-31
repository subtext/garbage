<?php

use function DI\create;
use function DI\get;
use function DI\string;

return [
    'application.root' => dirname(__DIR__, 2),
    'templates.root' => string('{application.root}/templates'),
    \Twig_Loader_Filesystem::class => create()->constructor(
        get('templates.root')
    ),
    \Twig_Environment::class => create()->constructor(
        get('Twig_Loader_Filesystem')
    )
];
