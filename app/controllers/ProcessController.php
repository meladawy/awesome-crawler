<?php

use \phpQuery;
use \Curl\Curl;

/**
 * Process Controller that handle the homepage for.
 */
class ProcessController extends Controller
{
  // I use this private variables for redirect.
  private $home_page =  FRONT_PATH;
  private $curl;
  private $website = '';
  private $output = '';

  function __construct () {
    $this->curl = new Curl();
    $this->curl->setOpt(CURLOPT_FOLLOWLOCATION, TRUE);
  }

  /**
   * Process handling function
   */
   public function index() {
     // Initialize variables
     $this->website = !empty($_POST['url']) ? $_POST['url'] : "";

     if(empty($this->website)) {
       echo "Something went wrong";
       return;
     }

     $this->curl->get($this->website);

     if ($this->curl->error) {
       echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
       return;
     }

     // $output contains the output string
     $this->output = $this->curl->response;

     var_dump($this->getImages());
     var_dump($this->getJS());
     var_dump($this->getCSS());

   }

   function getImages() {
     $images_array = [];
     $page_markup = phpQuery::newDocumentHTML($this->output, $charset = 'utf-8');
     $images = pq("img", $page_markup);

     foreach($images as $image) {
       $images_array[] = pq($image)->attr("src");
     }

     return $images_array;
   }

   function getJS() {
     $js_array = [];
     $page_markup = phpQuery::newDocumentHTML($this->output, $charset = 'utf-8');
     $js = pq("script", $page_markup);

     foreach($js as $js_item) {
       if(!empty(pq($js_item)->attr("src"))) {
         $js_array[] = pq($js_item)->attr("src");
       }
     }

     return $js_array;
   }

   function getCSS() {
     $css_array = [];
     $page_markup = phpQuery::newDocumentHTML($this->output, $charset = 'utf-8');
     $css = pq("link[rel='stylesheet']", $page_markup);

     foreach($css as $css_item) {
       if(!empty(pq($css_item)->attr("href"))) {
         $css_array[] = pq($css_item)->attr("href");
       }
     }

     return $css_array;
   }
}
