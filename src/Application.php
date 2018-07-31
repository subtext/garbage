<?php
/**
 * Created by PhpStorm.
 * User: subtext
 * Date: 2/1/18
 * Time: 12:52 PM
 */

namespace Subtext\Garbage;


class Application
{
    /**
     * @var Controller
     */
    private $controller;

    public function __construct(Controller $controller)
    {
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
