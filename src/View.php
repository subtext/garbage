<?php

namespace Subtext\Garbage;


class View
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * View constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
        $this->twig->addExtension(new \Twig_Extensions_Extension_I18n());
        $this->twig->addExtension(new \Twig_Extensions_Extension_Intl());
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
