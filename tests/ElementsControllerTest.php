<?php

/**
 * @file
 * Create a test case for elements controller.
 */

 use GuzzleHttp\Client;

/**
 * Test case for ElementsController.
 */
class ElementsControllerTest extends \PHPUnit_Framework_TestCase {
  private $base_url = 'http://localhost/awesome-crawler/web'; // Replace it with your local directory path
  private $post_param_urls = 'http://gocardless.com'; // Parameter value
  private $post_param_elements = 'images,js'; // Parameter value
  private $response_contain_value = 'http://gocardless.com/images/logos/box.png'; // Expected output compare

  /**
   * Test Post Request to a specific URL.
   */
  public function testPOST() {
    // Create our http client (Guzzle)
    $client = new Client();

    $response = $client->request('POST', $this->base_url . '/ajax/elements', [
      'form_params' => [
        'urls' => $this->post_param_urls,
        'elements' => $this->post_param_elements,
      ],
    ]);

    $this->assertEquals(200, $response->getStatusCode(), "Incorrect response code");
    $data = json_decode($response->getBody(TRUE), TRUE);
    $this->assertNotEmpty($data, "Empty response body");
    $this->assertGreaterThan(0, count($data), "Empty Response Array");
    $this->assertContains($this->response_contain_value, $data[0]['assets'], "Response doesn't contain : " . $this->response_contain_value);

  }

}
