<?php

namespace Subtext\Garbage;

class Controller
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
     * Controller constructor.
     * @param Model $model
     * @param View $view
     */
    public function __construct(Model $model, View $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    /**
     * Execute the application controller
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function execute()
    {
        $this->view->setInternationalization(
            $this->model->getTranslator()
        );
        $output = $this->view->display(
            $this->model->getTemplate(),
            $this->model->getData()
        );
        echo $output;
    }

}
