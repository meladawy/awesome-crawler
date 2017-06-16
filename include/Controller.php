<?php
// Autoloader.
require_once LIB_PATH. '/autoload.php';

/**
* Base controller
*/
class Controller
{
    const TEMPLATE_PATH = BASE_PATH. '/app/views' ;
    const TEMPLATE_CACHE_PATH = BASE_PATH. '/cache/twig' ;
    protected $loader = null ;
    protected $twig = null ;
    /**
  * Constructor function to initialize twig variables
  */
    function __construct()
    {
        // Register Twig
        Twig_Autoloader::register();
        // Initialize the loader
        $this->loader = new Twig_Loader_Filesystem(self::TEMPLATE_PATH);
        // Setup twig environment
        $this->twig = new Twig_Environment($this->loader);
        // pass global variables
        // HOMEPAGE URL
        $this->twig->addGlobal('front_page', FRONT_PATH);
    }
    /**
  * Render function that will render twig template by name
  */
    public function render($template, $variables = array())
    {
        return $this->twig->render($template, $variables);
    }
}
