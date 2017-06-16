<?php
/**
* Custom Router created that use Macaw router and handle the overall request
*/

/**
* Include external libs
*/
// include the router and use the name space
// I used Macaw router from https://github.com/noahbuscher/Macaw
require_once(LIB_PATH. '/autoload.php');
use \NoahBuscher\Macaw\Macaw;



Class Router
{
  /**
  * Load all routes
  * @param array $routes['controller', 'action']
  */
  function loadRoutes($routes) {
    $current_path = request_path() ;
    // By default there is no route found
    $route_found = 0 ;
    foreach($routes as $path => $route_info) {
      if(!empty($route_info['controller']) && ($current_path == $path)) {
        // route matched then execute the controller method
          // To avoid 404 message
          $route_found = 1;
          // Initialize object of the Controller
          // if class not exist then i'll print an error message
          if(class_exists($route_info['controller'])) {
            $obj = new $route_info['controller'] ;
          }else{
            exit("You forgot to define the class : " . $route_info['controller']);
          }
          // If GET Request
          if($_SERVER['REQUEST_METHOD'] == "GET") {
            Macaw::get($path, $obj->$route_info['action']());
          }
          // If POST Request
          if($_SERVER['REQUEST_METHOD'] == "POST") {
            Macaw::post($path, $obj->$route_info['action']());
          }
          //.. we can add other request methods

      }
    }
    // If route not found then drop 404 error message
    if($route_found == 0) {
      Macaw::error(function() {
        echo '404 :: Not Found';
      });
      Macaw::dispatch();
    }

  }
}
