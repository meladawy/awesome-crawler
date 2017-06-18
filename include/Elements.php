<?php

/**
 * @file
 * Elements class that load and parse all elements.
 */

 // Include the defined elements.
 include BASE_PATH . '/config/elements.php';

class Elements {

  /**
   * Get array of elements defined in configuration array.
   *
   * @return array
   */
  public function get_elements () {
    global $elements;
    $elements_array = array();

    foreach ($elements as $name => $element) {
      $element['name'] = $name;
      $elements_array[] = $element;
    }

    return $elements_array;
  }

  /**
   * Get element array by name.
   *
   * @param string $name
   *   Element name that we will query with.
   *
   * @return array
   */
  public function get_element_by_name ($name) {
    $elements = $this->get_elements();

    if($elements) {
      foreach ($elements as $element) {
        if($element['name'] == $name) {
          return $element;
        }
      }
    }

    return false;
  }
}
