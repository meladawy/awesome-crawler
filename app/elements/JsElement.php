<?php

/**
 * @file
 * Define new element of type "image".
 */

use \phpQuery;

/**
 * Get JS files for current markup.
 */
class JsElement {

  /**
   * Get JS files for current markup.
   *
   * @return array
   *   Array of js files attached to current website marup.
   */
  public static function output($markup) {
    $js_array = [];
    $page_markup = phpQuery::newDocumentHTML($markup, $charset = 'utf-8');
    $js = pq("script", $page_markup);

    foreach ($js as $js_item) {
      if (!empty(pq($js_item)->attr("src"))) {
        $js_array[] = pq($js_item)->attr("src");
      }
    }

    return array_merge(array_unique($js_array), array());
  }

}
