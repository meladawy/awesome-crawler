<?php

/**
 * @file
 * Define new element of type "image".
 */

use \phpQuery;

/**
 * Get CSS files for current markup.
 */
class CssElement {

  /**
   * Get CSS files for current markup.
   *
   * @return array
   *   Array of css files attached to current website marup.
   */
  public static function output($markup) {
    $css_array = [];
    $page_markup = phpQuery::newDocumentHTML($markup, $charset = 'utf-8');
    $css = pq("link[rel='stylesheet']", $page_markup);

    foreach ($css as $css_item) {
      if (!empty(pq($css_item)->attr("href"))) {
        $css_array[] = pq($css_item)->attr("href");
      }
    }

    return array_merge(array_unique($css_array), array());
  }

}
