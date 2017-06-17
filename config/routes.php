<?php

/**
 * @file
 * Define routes as an array.
 */

$routes = array(
  '/' => array('controller' => 'HomeController', 'action' => 'index'),
  'process' => array('controller' => 'ProcessController', 'action' => 'index'),
  'ajax/elements' => array('controller' => 'ElementsController', 'action' => 'index'),
);
