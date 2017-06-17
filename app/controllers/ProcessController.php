<?php

use \phpQuery;
use Curl\Curl;

/**
 * Process Controller that handle the homepage for.
 */
class ProcessController extends Controller {
  /**
   * I use this private variables for redirect.
   */
  private $home_page = FRONT_PATH;
  private $curl;
  private $website = '';
  private $response = array();
  private $jsonHelper;

  /**
   * Constructor function to initialize curl funciton.
   */
  public function __construct() {
    $this->curl = new Curl();
    $this->curl->setOpt(CURLOPT_FOLLOWLOCATION, TRUE);

    $this->jsonHelper = new JsonHelper();
  }

  /**
   * Process handling function.
   */
  public function index() {
    // Initialize variables.
    $this->website = !empty($_POST['url']) ? $_POST['url'] : "";
    $response_elements = !empty($_POST['elements']) ? $_POSt['elements'] : array();
    $recurrsive = !empty($_POST['recurrsive']) ? TRUE : FALSE;

    // Check Whether website submitted or not.
    if (empty($this->website)) {
      print $this->jsonHelper->setError("We didn't find the URL");
      return;
    }

    // Retrieve HTML Markup for the page.
    $this->curl->get($this->website);

    // If Something went wrong.
    if ($this->curl->error) {
      print $this->jsonHelper->setError($this->curl->errorMessage, $this->curl->errorCode);
      return;
    }

    // Set output markup for current URL.
    $this->output = $this->curl->response;

    // Get links in current website.
    // $this->updateResponse($this->website, $this->output);
    $links = $this->getLinks($this->output);
    $counters = 0;
    if(count($links) > 0) {
      foreach($links as $link) {
        $this->updateResponse($link);
        $counters++;

        if($counters == 4)
        break;
      }
    }

    print json_encode($this->response, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
  }

  /**
   * Get images for current markup.
   */
  private function getImages($markup) {
    $images_array = [];
    $page_markup = phpQuery::newDocumentHTML($markup, $charset = 'utf-8');
    $images = pq("img", $page_markup);

    foreach ($images as $image) {
      $images_array[] = pq($image)->attr("src");
    }

    return $images_array;
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

    return $js_array;
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

    return $css_array;
  }

  /**
   * Get Links inside current markup. We only filter links that related to current website and skip any link for any external website.
   *
   * @return array
   *   Array of links.
   */
  public function getLinks($markup) {
    $links_array = [];
    $page_markup = phpQuery::newDocumentHTML($markup, $charset = 'utf-8');
    // Get all links that point to current website.
    $website_without_prefixes = $this->getDomainFromUrl($this->website);
    $relative_links = pq("a[href^='/'], a[href^='./'], a[href^='../']", $page_markup);
    $absolute_links = pq("a[href^='http://'], a[href^='https://'], a[href^='www.']", $page_markup)->filter("a[href*='$website_without_prefixes']");

    foreach ($relative_links as $link) {
      if (!empty(pq($link)->attr("href"))) {
        $links_array[] = $this->website . pq($link)->attr("href");
      }
    }

    foreach ($absolute_links as $link) {
      $crawled_link_domain = $this->getDomainFromUrl(pq($link)->attr("href"));
      $link_domain_match_target_domain = ($crawled_link_domain === $website_without_prefixes) ? TRUE : FALSE;
      if (!empty(pq($link)->attr("href")) &&  $link_domain_match_target_domain) {
        $links_array[] = pq($link)->attr("href");
      }
    }

    return $links_array;
  }

  private function updateResponse($url, $markup = "") {
    $response_item = array();
    $response_item['url'] = $url;
    $response_item['assets'] = array();

    // Set output markup for current URL.
    $markup = !empty($markup) ? $markup : $this->getMarkupByURL($url);
    $images = $this->getImages($markup);
    $js = $this->getJS($markup);
    $css = $this->getCSS($markup);

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

  private function getMarkupByURL($url) {
    // Retrieve HTML Markup for the page.
    $this->curl->get($url);

    // If Something went wrong.
    if ($this->curl->error) {
      return false;
    }

    // Set output markup for current URL.
    $markup = $this->curl->response;

    return $markup;
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
    return !empty($matches[1][0]) ? $matches[1][0] : FALSE;
  }
}
