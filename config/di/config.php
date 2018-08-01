<?php

use function DI\create;
use function DI\get;
use function DI\string;
use function DI\factory;
use Symfony\Component\HttpFoundation\Request;

return [
    'application.root' => dirname(__DIR__, 2),
    'application.locale' => 'en_US',
    'templates.root' => 'templates',
    Request::class => factory([Request::class, 'createFromGlobals']),
    \Twig_Loader_Filesystem::class => create()->constructor(
        get('templates.root'),
        get('application.root')
    ),
    \Twig_Environment::class => create()->constructor(
        get('Twig_Loader_Filesystem')
    ),
    Symfony\Component\Translation\Translator::class => create()->constructor(
        get('application.locale')
    )
];
