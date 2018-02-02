<?php
/**
 * Created by PhpStorm.
 * User: subtext
 * Date: 2/1/18
 * Time: 1:02 PM
 */

namespace Subtext\Garbage;

use Symfony\Component\HttpFoundation\Request;

class Model
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Return the http request method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->request->getMethod();
    }

    /**
     * Return an http request parameter
     *
     * @param string $key
     * @return mixed
     */
    public function getParameter(string $key, string $default = '')
    {
        return $this->request->get($key, $default);
    }

    /**
     * Parse all pertinent request headers for application controller logic
     *
     * @return string
     */
    public function getRequestForController()
    {
        return $this->getParameter('command', 'default');
    }
}
