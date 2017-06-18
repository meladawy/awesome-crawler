<?php

use \phpQuery;
use Curl\Curl;

/**
 * Links Controller that retrieve links for a particular page.
 */
class LinksController extends Controller {
  /**
   * I use this private variables for redirect.
   */
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
    $this->url = !empty($_POST['url']) ? $_POST['url'] : "";

    // Check Whether website submitted or not.
    if (empty($this->url)) {
      print $this->jsonHelper->setError("URL Parameter is missing");
      return;
    }

    // Retrieve HTML Markup for the page.
    $this->curl->get($this->url);

    // If Something went wrong.
    if ($this->curl->error) {
      print $this->jsonHelper->setError($this->curl->errorMessage, $this->curl->errorCode);
      return;
    }

    // Set output markup for current URL.
    $markup = $this->curl->response;

    // Get links in current website.
    $links = $this->getLinks($markup);

    print json_encode($links, JSON_UNESCAPED_SLASHES);
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
        $links_array[] = $this->curl->url . pq($link)->attr("href");
      }
    }

    foreach ($absolute_links as $link) {
      $crawled_link_domain = $this->getDomainFromUrl(pq($link)->attr("href"));
      $link_domain_match_target_domain = ($crawled_link_domain === $website_without_prefixes) ? TRUE : FALSE;
      if (!empty(pq($link)->attr("href")) &&  $link_domain_match_target_domain) {
        $links_array[] = pq($link)->attr("href");
      }
    }

    return array_merge(array_unique($links_array), array());
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
