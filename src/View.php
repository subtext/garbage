<?php
/**
 * Created by PhpStorm.
 * User: subtext
 * Date: 2/1/18
 * Time: 1:03 PM
 */

namespace Subtext\Garbage;


class View
{
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Render the view
     *
     * @param string $template
     * @param array $data
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function display(string $template, array $data): string
    {
        return $this->twig->render($template, $data);
    }
}
