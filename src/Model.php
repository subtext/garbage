<?php

namespace Subtext\Garbage;

use Subtext\Garbage\Services\Localization;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Translator;

class Model
{
    /**
     * @var Request
     */
    private $request;

    private $localization;

    private $translator;


    public function __construct(
        Request $request,
        Localization $localization,
        Translator $translator
    )
    {
        $this->request = $request;
        $this->localization = $localization;
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
