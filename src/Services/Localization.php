<?php

namespace Subtext\Garbage\Services;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\LoaderInterface;

final class Localization
{
    /**
     * A regular expression for parsing the Accept-Language header
     */
    private const LOCALE_PATTERN = '/^([a-z]{1,8}(?:[-_][a-z]{1,8})*)(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i';

    /**
     * An ordered array of acceptable locales and languages. The first matching
     * locale will set the preferred language for the site.
     *
     * @var string[]
     */
    private $_accepted = [];

    /**
     * The default language to be used. This will also be used as the fallback
     * if a translation in another language is missing an entry.
     *
     * @var string
     */
    private $_default;

    /**
     * The definitive locale to be used for translations
     *
     * @var string
     */
    private $_locale;

    /**
     * @var Translator
     */
    private $_translator;

    /**
     * @var LoaderInterface
     */
    private $_loader;

    /**
     * An array of loaded domains
     *
     * @var array
     */
    private $_domains = [];

    /**
     * Path to resource directory
     *
     * @var string
     */
    private $_resourceDir;

    /**
     * The file extension to be used for resource files
     *
     * @var string
     */
    private $_extension;

    /**
     * Localization service constructor.
     *
     * @param Translator      $translator
     * @param LoaderInterface $loader
     * @param array           $accepted
     * @param string          $resourceDir
     * @param string          $extension
     */
    public function __construct(
        Translator $translator,
        LoaderInterface $loader,
        array $accepted,
        string $resourceDir,
        string $extension
    )
    {
        $this->_translator  = $translator;
        $this->_loader      = $loader;
        $this->_default     = $translator->getLocale();
        $this->_accepted    = $accepted;
        $this->_resourceDir = $resourceDir;
        $this->_extension   = $extension;
        $this->_translator->addLoader('default', $this->_loader);
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale(string $locale): void
    {
        $this->_locale = $locale;
        $this->_translator->setLocale($this->_locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): string
    {
        return $this->_locale;
    }

    /**
     * {@inheritdoc}
     */
    public function translate(
        string $key,
        array $args    = [],
        string $domain = null
    ): string
    {
        if (is_null($domain)) {
            $domain = 'messages';
        } else {

            // allows domain to be lazily loaded
            if (!in_array($domain, $this->_domains)) {
                $this->loadDomain($domain);
            }
        }
        return $this->_translator->trans($key, $args, $domain);
    }

    /**
     * {@inheritdoc}
     */
    public function plural(
        string $key,
        int $num,
        array $args    = [],
        string $domain = null
    ): string
    {
        return $this->_translator->transChoice($key, $num, $args, $domain);
    }

    /**
     * Update critical resource info related to locale. This method must be
     * called if the locale is updated outside of the DI container
     *
     * @param string[] $accepted    An array of locales in order of preference
     * @param string   $resourceDir The directory from which resource files are loaded
     * @param string   $extension   The file extension used with resource files
     */
    public function setDefaults(
        array $accepted,
        string $resourceDir,
        string $extension
    ): void
    {
        $this->_accepted    = $accepted;
        $this->_resourceDir = $resourceDir;
        $this->_extension   = $extension;
        $this->loadDomain('messages');
    }

    /**
     * Set default and fallback locale as well as adding default loader
     */
    public function configureFromEnvironment(): void
    {
        if (!\file_exists($this->_resourceDir)) {
            throw new \InvalidArgumentException();
        }
        $this->getLocaleFromBrowser();
        $this->_translator->setLocale($this->_locale);
        $this->_translator->setFallbackLocales([$this->_default]);
        $this->loadDomain('messages');
    }

    /**
     * @param string $domain
     * @throws \InvalidArgumentException
     */
    private function loadDomain(string $domain): void
    {
        $path = $this->getResourcePath($domain);
        $this->_translator->addResource('default', $path, $this->_locale, $domain);
        if (!in_array($domain, $this->_domains)) {
            $this->_domains[] = $domain;
        }
    }

    /**
     * Get the path to a file resource based on domain
     *
     * @param string $domain
     * @return string
     */
    private function getResourcePath(string $domain): string
    {
        $path = $this->_resourceDir
            . "/{$domain}"
            . ".{$this->_locale}"
            . ".{$this->_extension}";
        if (!\file_exists($path)) {
            throw new \InvalidArgumentException(
                "The resource file {$path} does not exist."
            );
        }
        return $path;
    }

    /**
     * Gets a list of acceptable language prefs from the browser and compares
     * them against a list of languages that are supported. The first locale in
     * order of preference is chosen and set as the default.
     */
    private function getLocaleFromBrowser(): void
    {
        $envLocales = \getenv('HTTP_ACCEPT_LANGUAGE');
        if (empty($envLocales) && \array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
            $envLocales = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }
        $acceptable = $this->parseAcceptedLanguages($envLocales);
        $available = \array_values(\array_intersect($acceptable, $this->_accepted));

        // If no acceptable locale is available use the default
        if (empty($available)) {
            $this->_locale = $this->_default;
        } else {
            $locale = \reset($available);

            // If only lang is set, loop through to find first matching locale
            if (\strpos($locale, '_') === false) {
                foreach($this->_accepted as $possible) {
                    if (\strpos($possible, $locale) === 0) {
                        $locale = $possible;
                        break;
                    }
                }
            }
            $this->_locale = $locale;
        }
    }

    /**
     * Returns an array of locales/languages in order of preference as specified
     * by the Accept-Language header of the http request
     *
     * @param string $value Section 5.3.5 of RFC 7231
     * @return array
     */
    private function parseAcceptedLanguages(string $value): array
    {
        $languages = [];
        if (empty($value)) {
            return $languages;
        }

        // Parse Accept-Language header with regular expression
        $entries = preg_split('/,\s*/', $value);
        foreach ($entries as $entry) {
            $match = null;
            $result = preg_match(self::LOCALE_PATTERN, $entry, $match);
            if ($result < 1) {
                continue;
            }
            if (isset($match[2]) === true) {
                $quality = (float)$match[2];
            } else {
                $quality = 1.0;
            }

            // An entry may have multiple countries per language
            $parts = explode('-', $match[1]);
            $lang   = array_shift($parts);

            // An entry may be a language without a country
            if (empty($parts)) {
                $languages[$lang] = $quality;
            } else {
                foreach ($parts as $country) {
                    $languages[$lang . '_' . strtoupper($country)] = $quality;
                }
            }
            if ((isset($languages[$lang]) === false) || ($languages[$lang] < $quality)) {
                $languages[$lang] = $quality;
            }
        }

        // Sort acceptable locales where 1.0 is the preferred $quality
        arsort($languages);
        return array_keys($languages);
    }
}
