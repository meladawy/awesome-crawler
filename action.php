<?php

/**
 * @file
 * Handle form submittion.
 */
require ('vendor/autoload.php');
use \phpQuery;
$website_url = !empty($_POST['url']) ? $_POST['url'] : "";

print($website_url);

// create curl resource
$ch = curl_init();

// set url
curl_setopt($ch, CURLOPT_URL, $website_url);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//return the transfer as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// $output contains the output string
$output = curl_exec($ch);
$page_markup = phpQuery::newDocumentHTML($output, $charset = 'utf-8');
$images = pq("img", $page_markup);
$js = pq("script", $page_markup);
$css = pq("link[rel='stylesheet']", $page_markup);
$images_array = [];
$js_array = [];
$css_array = [];

foreach($images as $image) {
  $images_array[] = pq($image)->attr("src");
}

foreach($js as $js_item) {
  if(!empty(pq($js_item)->attr("src"))) {
    $js_array[] = pq($js_item)->attr("src");
  }
}

foreach($css as $css_item) {
  if(!empty(pq($css_item)->attr("href"))) {
    $css_array[] = pq($css_item)->attr("href");
  }
}
print "<h3>IMAGES</h3>";
var_dump($images_array);
print "<h3>JS</h3>";
var_dump($js_array);
print "<h3>CSS</h3>";
var_dump($css_array);


echo curl_errno($ch) . '<br/>';
echo curl_error($ch) . '<br/>';


// close curl resource to free up system resources
curl_close($ch);

var_dump($output);
