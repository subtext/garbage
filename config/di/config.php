<?php

use function DI\create;
use function DI\get;
use function DI\string;
use function DI\factory;
use Psr\Container\ContainerInterface;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Translation\Loader\FileLoader;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

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
    'twig.resources' => 'vendor/symfony/twig-bridge/Resources/views/Form',
    'twig.theme' => 'bootstrap_4_layout.html.twig',
    Session::class => create(),
    Request::class => factory([Request::class, 'createFromGlobals']),
    TokenGeneratorInterface::class => create(UriSafeTokenGenerator::class),
    TokenStorageInterface::class => create(SessionTokenStorage::class)
        ->constructor(get(Session::class)),
    CsrfTokenManagerInterface::class => create(CsrfTokenManager::class)->constructor(
        get(TokenGeneratorInterface::class),
        get(TokenStorageInterface::class)
    ),
    \Twig_Loader_Filesystem::class => create()->constructor(
        [get('templates.root'), get('twig.resources')],
        get('application.root')
    ),
    \Twig_Environment::class => create()->constructor(
        get(\Twig_Loader_Filesystem::class),
        get('twig.options')
    ),
    TwigRendererEngine::class => create()->constructor(
        [get('twig.theme')],
        get(\Twig_Environment::class)
    ),
    FormRenderer::class => create()->constructor(
        get(TwigRendererEngine::class),
        get(CsrfTokenManagerInterface::class)
    ),
    Translator::class => create()->constructor(
        get('application.locale'),
        get('Subtext\Garbage\Services\MessageFormatter')
    ),
    FileLoader::class => create(YamlFileLoader::class),
    Subtext\Garbage\Services\Localization::class => create()->constructor(
        get(Translator::class),
        get(FileLoader::class),
        get('application.locales'),
        get('application.i18n.dir'),
        get('application.i18n.ext')
    )
];
