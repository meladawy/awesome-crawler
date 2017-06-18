<?php

/**
 * @file
 * Entry point to load Controllers, Router and any other stuff.
 */

// THE BASE PATH FOR THE PROJECT.
define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
// CONTROLLERS PATH.
define('CONTROLLERS_PATH', BASE_PATH . '/app/controllers/');
// EXT LIBRARY PATH.
define('LIB_PATH', BASE_PATH . '/vendor/');
// HELPERS FUNCTIONS PATH.
define('HELPERS_PATH', BASE_PATH . '/include/');
// CUSTOM HANDLERS PATH.
define('CUSTOM_HELPERS_PATH', BASE_PATH . '/app/helpers');
// CUSTOM ELEMENTS PATH.
define('CUSTOM_ELEMENTS_PATH', BASE_PATH . '/app/elements');


/**
* Including and Initializing external libs.
*/
// Include http functions.
include HELPERS_PATH . '/http.php';
// Include the router.
include HELPERS_PATH . '/Router.php';
// Include the base controller.
include HELPERS_PATH . '/Controller.php';
// Include the base controller.
include HELPERS_PATH . '/Elements.php';
// Include the defined routes.
include BASE_PATH . '/config/routes.php';

/**
* FRONT PATH variable depends on the router request_path() function.
*/

if (request_path() == "/") {
  define('FRONT_PATH', str_replace(request_path(), "/", "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']));
}
else {
  define('FRONT_PATH', str_replace(request_path(), "", "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']));
}

/**
 * Load the Controllers, Handlers and Elements.
 *
 * @param string $class
 *   Class name to search for.
 */
function cawler_autoloader($class) {
  // Autoload Controllers.
  if (strpos($class, 'Controller') !== FALSE) {
    $classFile = CONTROLLERS_PATH . DIRECTORY_SEPARATOR . $class . '.php';
  }

  // Autoload Helpers.
  if (strpos($class, 'Helper') !== FALSE) {
    $classFile = CUSTOM_HELPERS_PATH . DIRECTORY_SEPARATOR . $class . '.php';
  }

  // Autoload Elements.
  if (strpos($class, 'Element') !== FALSE && strpos($class, 'Controller') == FALSE) {
    $classFile = CUSTOM_ELEMENTS_PATH . DIRECTORY_SEPARATOR . $class . '.php';
  }

  if (is_file($classFile)&&!class_exists($class)) {
    include $classFile;
  }
}

spl_autoload_register('cawler_autoloader');

$router = new Router();
$router->loadRoutes($routes);
