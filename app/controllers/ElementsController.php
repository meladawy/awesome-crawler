<?php

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

  /**
   * Constructor function to initialize curl funciton.
   */
  public function __construct() {
    $this->curl = new MultiCurl();
    $this->curl->setOpt(CURLOPT_FOLLOWLOCATION, TRUE);

    $this->jsonHelper = new JsonHelper();
    $this->response = array();
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

    foreach($this->urls as $url) {
      $this->curl->addGet($url);
    }

    $this->curl->start();

    print $this->jsonHelper->indent(json_encode($this->response, JSON_UNESCAPED_SLASHES));
  }

  private function updateResponse($url, $markup) {
    $response_item = array();
    $response_item['url'] = $url;
    $response_item['assets'] = array();

    $images = (in_array("images", $this->elements)) ? $this->getImages($markup) : array();
    $js = (in_array("js", $this->elements)) ? $this->getJS($markup) : array();
    $css = (in_array("css", $this->elements)) ? $this->getCSS($markup) : array();

    if(count($images) > 0) {
      foreach($images as $image) {
        $response_item['assets'][] = $image;
      }
    }

    if(count($js) > 0) {
      foreach($js as $js_item) {
        $response_item['assets'][] = $js_item;
      }
    }

    if(count($css) > 0) {
      foreach($css as $css_item) {
        $response_item['assets'][] = $css_item;
      }
    }

    $this->response[] = $response_item;
  }

  /**
   * Get images for current markup.
   */
  private function getImages($markup) {
    $images_array = [];
    $page_markup = phpQuery::newDocumentHTML($markup, $charset = 'utf-8');
    $images = pq("img", $page_markup);

    foreach ($images as $image) {
      if (!empty(pq($image)->attr("src"))) {
        $images_array[] = pq($image)->attr("src");
      }
    }

    return array_unique($images_array);
  }

  /**
   * Get JS files in current markup.
   */
  private function getJS($markup) {
    $js_array = [];
    $page_markup = phpQuery::newDocumentHTML($markup, $charset = 'utf-8');
    $js = pq("script", $page_markup);

    foreach ($js as $js_item) {
      if (!empty(pq($js_item)->attr("src"))) {
        $js_array[] = pq($js_item)->attr("src");
      }
    }

    return array_unique($js_array);
  }

  /**
   * Get CSS files for current markup.
   *
   * @return array
   *   Array of css files attached to current website marup.
   */
  private function getCSS($markup) {
    $css_array = [];
    $page_markup = phpQuery::newDocumentHTML($markup, $charset = 'utf-8');
    $css = pq("link[rel='stylesheet']", $page_markup);

    foreach ($css as $css_item) {
      if (!empty(pq($css_item)->attr("href"))) {
        $css_array[] = pq($css_item)->attr("href");
      }
    }

    return array_unique($css_array);
  }

}
