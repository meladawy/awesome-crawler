<?php

/**
 * @file
 * Create a test case for links controller.
 */

 use GuzzleHttp\Client;

/**
 * Test case for LinksController.
 */
class LinksControllerTest extends \PHPUnit_Framework_TestCase {
  private $base_url = 'http://localhost/awesome-crawler/web'; // Replace it with your local directory path
  private $post_param_url = 'http://gocardless.com'; // Parameter value
  private $response_contain_value = 'http://gocardless.com/about/'; // Expected output compare

  /**
   * Test Post Request to a specific URL.
   */
  public function testPOST() {
    // Create our http client (Guzzle)
    $client = new Client();

    $response = $client->request('POST', $this->base_url . '/ajax/links', [
      'form_params' => [
        'url' => $this->post_param_url,
      ],
    ]);

    $this->assertEquals(200, $response->getStatusCode(), "Incorrect response code");
    $data = json_decode($response->getBody(TRUE), TRUE);
    $this->assertNotEmpty($data, "Empty response body");
    $this->assertGreaterThan(0, count($data), "Empty Response Array");
    $this->assertContains($this->response_contain_value, $data, "Response doesn't contain : " . $this->response_contain_value);
  }

}
