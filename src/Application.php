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
     * @var Model
     */
    private $model;

    /**
     * @var View
     */
    private $view;

    /**
     * @var Controller
     */
    private $controller;

    public function __construct(Model $model, View $view, Controller $controller)
    {
        $this->model = $model;
        $this->view = $view;
        $this->controller = $controller;
    }

    public function execute()
    {
        echo "hello, alonzo!";
    }
}
