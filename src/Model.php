<?php

namespace Subtext\Garbage;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\YamlFileLoader;

class Model
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Translator
     */
    private $translator;

    private $languages = ['en_US', 'en_GB', 'en', 'es_ES', 'es', 'fr_FR', 'fr'];

    public function __construct(Request $request, Translator $translator)
    {
        $this->request = $request;
        $this->translator = $translator;
    }

    /**
     * Determine the name of the template to be rendered
     *
     * @return string
     */
    public function getTemplate(): string
    {
        return 'default.twig';
    }

    /**
     * Get all data pertinent to the view
     *
     * @return array
     */
    public function getData(): array
    {
        return [
            'pageTitle' => 'headline.primary',
            'pageContent' => 'headline.secondary.',
            'colors' => [
                'red' => 'color.red',
                'blue' => 'color.blue',
                'green' => 'color.green',
            ],
        ];
    }

    /**
     * Set default locale from the browser and return Translator
     *
     * @return Translator
     */
    public function getTranslator(): Translator
    {
        $langs = $this->request->getLanguages();
        $available = array_intersect($langs, $this->languages);
        $locale = array_shift($available);
        $lang = explode('_', $locale)[0];
        $path = '../i18n/messages.' . $lang . '.yaml';

        // Set the environment locale
        $this->request->setLocale($locale);
        $this->translator->setLocale($locale);

        // Load the translations
        $this->translator->addLoader('yaml', new YamlFileLoader());
        $this->translator->addResource('yaml', $path, $locale);

        return $this->translator;
    }

    /**
     * Return the http request method
     *
     * @return string
     */
    protected function getMethod()
    {
        return $this->request->getMethod();
    }

    /**
     * Return an http request parameter
     *
     * @param string $key
     * @return mixed
     */
    protected function getParameter(string $key, string $default = '')
    {
        return $this->request->get($key, $default);
    }
}
