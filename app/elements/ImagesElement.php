<?php

/**
 * @file
 * Define new element of type "image".
 */

use \phpQuery;

/**
 * Get Images exist in current markup.
 */
class ImagesElement {

  /**
   * Get Images exist in current markup.
   *
   * @return array
   *   Array of images files attached to current website markup.
   */
  public static function output($markup) {
    $images_array = [];
    $page_markup = phpQuery::newDocumentHTML($markup, $charset = 'utf-8');
    $images = pq("img", $page_markup);

    foreach ($images as $image) {
      if (!empty(pq($image)->attr("src"))) {
        $images_array[] = pq($image)->attr("src");
      }
    }

    return array_merge(array_unique($images_array), array());
  }

}
