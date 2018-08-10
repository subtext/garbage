<?php

use function DI\create;
use function DI\get;
use function DI\string;
use function DI\factory;
use Symfony\Component\HttpFoundation\Request;

return [
    'application.root' => dirname(__DIR__, 2),
    'application.locale' => 'en_US',
    'application.locales' => ['en_US', 'en_CA', 'es_MX', 'fr_CA'],
    'application.i18n.dir' => string('{application.root}/i18n'),
    'application.i18n.ext' => 'yml',
    'templates.root' => 'templates',
    'twig.options' => [
        'debug' => true,
        'charset' => 'utf-8',
        'cache' => string('{application.root}/templates/cache')
    ],
    Request::class => factory([Request::class, 'createFromGlobals']),
    \Twig_Loader_Filesystem::class => create()->constructor(
        get('templates.root'),
        get('application.root')
    ),
    \Twig_Environment::class => create()->constructor(
        get('Twig_Loader_Filesystem'),
        get('twig.options')
    ),
    Symfony\Component\Translation\Translator::class => create()->constructor(
        get('application.locale'),
        get('Subtext\Garbage\Services\MessageFormatter')
    ),
    Symfony\Component\Translation\Loader\FileLoader::class => create(
        Symfony\Component\Translation\Loader\YamlFileLoader::class
    ),
    Subtext\Garbage\Services\Localization::class => create()->constructor(
        get('Symfony\Component\Translation\Translator'),
        get('Symfony\Component\Translation\Loader\FileLoader'),
        get('application.locales'),
        get('application.i18n.dir'),
        get('application.i18n.ext')
    )
];
