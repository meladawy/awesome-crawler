<?php

/**
 * @file
 * Define routes as an array.
 */

$routes = array(
  '/' => array('controller' => 'HomeController', 'action' => 'index'),
  'ajax/links' => array('controller' => 'LinksController', 'action' => 'index'),
  'ajax/elements' => array('controller' => 'ElementsController', 'action' => 'index'),
);
