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
    public function getParameter(string $key)
    {
        return $this->request->get($key);
    }
}
