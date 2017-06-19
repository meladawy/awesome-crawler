<?php

/**
 * @file
 * Create a test case for links controller.
 */

 use GuzzleHttp\Client;

define('BASE_URL', 'http://localhost/awesome-crawler/web');
define('POST_PARAM_URL', 'http://gocardless.com');
define('RESPONSE_CONTAIN_VALUE', 'http://gocardless.com/about/');


/**
 * Test case for LinksController.
 */
class LinksControllerTest extends \PHPUnit_Framework_TestCase {

  /**
   * Test Post Request to a specific URL.
   */
  public function testPOST() {
    // Create our http client (Guzzle)
    $client = new Client();

    $response = $client->request('POST', BASE_URL . '/ajax/links', [
      'form_params' => [
        'url' => POST_PARAM_URL,
      ],
    ]);

    $this->assertEquals(200, $response->getStatusCode(), "Incorrect response code");
    $data = json_decode($response->getBody(TRUE), TRUE);
    $this->assertNotEmpty($data, "Empty response body");
    $this->assertGreaterThan(0, count($data), "Empty Response Array");
    $this->assertContains(RESPONSE_CONTAIN_VALUE, $data, "Response doesn't contain : " . RESPONSE_CONTAIN_VALUE);
  }

}
