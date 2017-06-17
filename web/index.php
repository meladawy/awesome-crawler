<?php

// Starts the session.
session_start();

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


/**
* Including and Initializing external libs
*/
// include basic model handler
include(BASE_PATH. '/app/models/handler/BasicDB.php') ;
// include http functions
include(HELPERS_PATH. '/http.php') ;
// include the router
include(HELPERS_PATH. '/Router.php') ;
// include the base controller
include(HELPERS_PATH. '/Controller.php') ;


/**
* FRONT PATH variable depends on the router request_path() function
*/

if(request_path() == "/")
define('FRONT_PATH', str_replace(request_path(), "/", "http://".$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']));
else
define('FRONT_PATH', str_replace(request_path(), "", "http://".$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']));

/**
* End of : Including and Initializing external libs
*/


// include the defined routes
include(BASE_PATH . '/config/routes.php');

/**
 * Load the Controllers and Handlers.
 *
 * @param string $class
 */
function controllersHandlersAutoLoader($class){
  // Autoload Controllers.
  if (strpos($class, 'Controller') !== false) {
    $classFile=CONTROLLERS_PATH.DIRECTORY_SEPARATOR.$class.'.php';
  }

  // Autoload Helpers.
  if (strpos($class, 'Helper') !== false) {
    $classFile=CUSTOM_HELPERS_PATH.DIRECTORY_SEPARATOR.$class.'.php';
  }

  if(is_file($classFile)&&!class_exists($class)) include $classFile;
}


spl_autoload_register('controllersHandlersAutoLoader');

$router = new Router ;
$router->loadRoutes($routes) ;
