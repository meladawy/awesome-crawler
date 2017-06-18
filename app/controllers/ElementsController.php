<?php

/**
 * @file
 * Output elements included in a specific website markup.
 */

use \phpQuery;
use Curl\MultiCurl;

/**
 * Elements Controller that fetch website elements for a specific pages.
 */
class ElementsController extends Controller {
  private $curl;
  private $urls;
  private $elements;
  private $jsonHelper;
  private $response;
  private $element_handler;

  /**
   * Constructor function to initialize curl funciton.
   */
  public function __construct() {
    $this->curl = new MultiCurl();
    $this->curl->setOpt(CURLOPT_FOLLOWLOCATION, TRUE);

    $this->jsonHelper = new JsonHelper();
    $this->response = array();
    $this->element_handler = new Elements();
  }

  /**
   * Callback function for ajax elements page.
   */
  public function index() {
    // Initialize variables.
    $this->urls = !empty($_POST['urls']) ? explode(",", $_POST['urls']) : "";
    $this->elements = !empty($_POST['elements']) ? explode(",", $_POST['elements']) : array();

    // Check Whether website submitted or not.
    if (empty($this->urls) || empty($this->elements)) {
      print $this->jsonHelper->setError("Not all the parameters has been submitted");
      return;
    }

    // If Something went wrong.
    $this->curl->error(function ($instance) use (&$error) {
    });

    $this->curl->success(function ($instance) use (&$complete) {
      $this->updateResponse($instance->url, $instance->response);
    });

    foreach ($this->urls as $url) {
      $this->curl->addGet($url);
    }

    $this->curl->start();

    print $this->jsonHelper->indent(json_encode($this->response, JSON_UNESCAPED_SLASHES));
  }

  /**
   * Build response object structure.
   */
  private function updateResponse($url, $markup) {
    $response_item = array();
    $response_item['url'] = $url;
    $response_item['assets'] = array();

    // Display each output from custom elements.
    foreach ($this->elements as $element_name) {
      $element = $this->element_handler->get_element_by_name($element_name);
      $element_output = call_user_func($element['class'] .'::output', $markup);
      // Convert relative urls to absolute.
      foreach($element_output as $key => $value) {
        if(substr($value, 0, 1) === '/' && substr($value, 0, 6) != '//www.') {
          $element_output[$key] = $this->getDomainFromUrl($url) . $value;
        }
      }
      // If group is not initialized before, then just initialize it.
      if(empty($response_item[$element['group']])) {
        $response_item[$element['group']] = array();
      }

      $response_item[$element['group']] = array_merge($response_item[$element['group']], $element_output) ;
    }

    $this->response[] = $response_item;
  }

  /**
   * Get domain name from url.
   *
   * @param string $url
   *   URL Path that we will extract the domain name of.
   *
   * @return string
   *   Domain name.
   */
  private function getDomainFromUrl($url) {
    $matches = array();
    preg_match_all("%^(?:https?://)?(?:[^@/n]+@)?(?:www.)?([^:/n]+)%", $url, $matches);
    return !empty($matches[0][0]) ? $matches[0][0] : '';
  }

}
