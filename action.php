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
$images = pq('img', $page_markup);
print "Lengths : {$images->length}";

foreach($images as $something) {
  var_dump(pq($something)->attr("src"));
}


var_dump(curl_getinfo($ch)) . '<br/>';
echo curl_errno($ch) . '<br/>';
echo curl_error($ch) . '<br/>';


// close curl resource to free up system resources
curl_close($ch);

var_dump($output);
