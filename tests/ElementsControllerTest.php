<?php

/**
 * @file
 * Create a test case for elements controller.
 */

 use GuzzleHttp\Client;

define('BASE_URL', 'http://localhost/awesome-crawler/web');
define('POST_PARAM_URLS', 'http://gocardless.com');
define('POST_PARAM_ELEMENTS', 'images,js');
define('RESPONSE_CONTAIN_VALUE', 'http://gocardless.com/images/logos/box.png');


/**
 * Test case for ElementsController.
 */
class ElementsControllerTest extends \PHPUnit_Framework_TestCase {

  /**
   * Test Post Request to a specific URL.
   */
  public function testPOST() {
    // Create our http client (Guzzle)
    $client = new Client();

    $response = $client->request('POST', BASE_URL . '/ajax/elements', [
      'form_params' => [
        'urls' => POST_PARAM_URLS,
        'elements' => POST_PARAM_ELEMENTS,
      ],
    ]);

    $this->assertEquals(200, $response->getStatusCode(), "Incorrect response code");
    $data = json_decode($response->getBody(TRUE), TRUE);
    $this->assertNotEmpty($data, "Empty response body");
    $this->assertGreaterThan(0, count($data), "Empty Response Array");
    $this->assertContains(RESPONSE_CONTAIN_VALUE, $data[0]['assets'], "Response doesn't contain : " . RESPONSE_CONTAIN_VALUE);

  }

}
