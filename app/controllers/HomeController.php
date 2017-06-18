<?php

/**
 * Home Controller that handle the homepage for.
 */
class HomeController extends Controller {
  /**
   * I use this private variables for redirect.
   */
  private $home_page = FRONT_PATH;

  /**
   * Homepage Landing function.
   */
  public function index() {
    $elements_handler = new Elements();
    $elements = $elements_handler->get_elements();

    $output = $this->render('home/home.html', array('elements' => $elements));
    echo $output;
  }

}
