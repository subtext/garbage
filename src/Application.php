<?php

namespace Subtext\Garbage;

use Subtext\Garbage\Controllers\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class Application
{
    private $session;
    /**
     * @var Controller
     */
    private $controller;

    public function __construct(Session $session, Controller $controller)
    {
        $this->session = $session;
        $this->controller = $controller;
    }

    /**
     * Execute the controller
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function execute()
    {
        $this->controller->execute();
    }
}
