<?php


/**
 * Home Controller that handle the homepage for.
 */
class HomeController extends Controller
{
  // I use this private variables for redirect.
  private $home_page =  FRONT_PATH ;

  /**
   * Homepage Landing function
   */
  public function index() {
    $output =  $this->render('home/home.html');
    echo $output;
  }
}
