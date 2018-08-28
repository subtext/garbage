<?php

use function DI\create;
use function DI\get;
use function DI\string;
use function DI\factory;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

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
    UriSafeTokenGenerator::class => create(),
    SessionTokenStorage::class => create()->constructor(get(Session::class)),
    CsrfTokenManager::class => create()->constructor(
        get(UriSafeTokenGenerator::class),
        get(SessionTokenStorage::class),
    ),
    Request::class => factory([Request::class, 'createFromGlobals']),
    \Twig\RuntimeLoader\FactoryRuntimeLoader::class => factory(function (ContainerInterface $c)),
    \Twig_Loader_Filesystem::class => create()->constructor(
        [get('templates.root'), get('twig.resources')],
        get('application.root')
    ),
    \Twig_Environment::class => factory(function (ContainerInterface $c) {
        $twig = new \Twig_Environment(
            $c->get('Twig_Loader_Filesystem'),
            $c->get('twig.options')
        );
        $engine = $c->get('\Symfony\Bridge\Twig\Form\TwigRendererEngine');
        $twig->addRuntimeLoader($c->get('\Twig\RuntimeLoader\FactoryRuntimeLoader'));

        return $twig;
    }),
    Symfony\Bridge\Twig\Form\TwigRendererEngine::class => create()->constructor(
        [get('twig.theme')],
        get(\Twig_Environment::class)
    ),
    Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator::class => create(),
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
