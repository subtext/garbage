<?php
/**
 * Created by PhpStorm.
 * User: subtext
 * Date: 2/1/18
 * Time: 1:02 PM
 */

namespace Subtext\Garbage;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Translator;

class Model
{
    /**
     * @var Request
     */
    private $request;

    private $translator;

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
            'pageTitle' => 'Welcome To My Page',
            'pageContent' => 'Bandit is a wonderful napping companion.',
            'colors' => [
                'red' => 'Red',
                'blue' => 'Blue',
                'green' => 'Green',
            ],
        ];
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
