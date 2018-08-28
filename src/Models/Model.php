<?php

namespace Subtext\Garbage\Models;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Form\Forms;
use Subtext\Garbage\Services\Localization;


class Model
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Localization
     */
    private $localization;

    /**
     * @var Translator
     */
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
            'myForm' => $this->getSampleForm()
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

    protected function getSampleForm()
    {
        $factory = Forms::createFormFactoryBuilder()->getFormFactory();
        $form = $factory->createBuilder()
                        ->add('name_first', 'text', [ 'label' => 'form.name.first' ])
                        ->add('name_middle', 'text', [ 'label' => 'form.name.middle' ])
                        ->add('name_last', 'text', [ 'label' => 'form.name.last' ])
                        ->add('name_suffix', 'choice', [
                            'choices' => [
                                'Jr.' => 'Jr.',
                                'Sr.' => 'Sr.',
                                'III' => 'III',
                                'IV' => 'IV'
                            ],
                            [ 'label' => 'form.name.suffix' ]
                        ])
                        ->getForm()
                        ->createView();

        return $form;
    }
}
