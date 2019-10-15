<?php

namespace FlashPHP\core\http;
use FlashPHP\core\http\Request\Request;
use FlashPHP\core\http\Response\Response;

/**
 * Router Class
 * @author Ingo Andelhofs
 * 
 * A class that lets u create new RESTful routes and also routes these so u can access them.
 * 
 * @uses Request class
 * @uses Response class
 */
class Router {
  // Variables
  private $route_name;
  private static $routes_data = [];

  // Constructor
  public function __construct($route_name) {
    $this->route_name = trim($route_name, '/');
  }

  // Functions
  public function get($method, $middleware_callback, $callback=null) { $this->req($method, 'GET', $middleware_callback, $callback); }
  public function post($method, $middleware_callback, $callback=null) { $this->req($method, 'POST', $middleware_callback, $callback); }
  public function put($method, $middleware_callback, $callback=null) { $this->req($method, 'PUT', $middleware_callback, $callback); }
  public function delete($method, $middleware_callback, $callback=null) { $this->req($method, 'DELETE', $middleware_callback, $callback); }

  public static function start() {
    self::include_route_folders();

    $_GET['url'] ?? die("U forgot to add '?url=' at the end of your url...");

    $url = self::parse_url();
    $req_method = self::get_request_method();

    $route = $url[0] ?? DEFAULT_ROUTER_ROUTE;
    $method = $url[1] ?? DEFAULT_ROUTER_METHOD;
    $params = isset($url[2]) ? array_slice($url, 2) : [];

    if (self::does_route_exist($route, $method, $req_method)) {
      self::activate($route, $method, $req_method, $params); 
    }
    else {
      self::suggest_and_activate($route, $method, $req_method, $params);
    }
  }


  private static function include_route_folders() {
    include_all_r(PATH_MIDDLEWARE);
    include_all_r(PATH_MODELS);
    include_all_r(PATH_ROUTES);
  }

  // Developing functions
  public static function print_all() {
    echo '<pre>';
    print_r( self::$routes_data );
    echo '</pre>';
  }
  
  // Helper functions
  private function req($method, $request_method, $middleware_callback, $callback=null) {
    // Split up method to get parameters (parameters -> routes_data)
    $method = self::parse_url($method);
    $method_name = $method[0] ?? DEFAULT_ROUTER_METHOD;
    $params = isset($method[1]) ? array_slice($method, 1) : [];

    // Create routes_data
    $full_route = &self::$routes_data[$this->route_name][$method_name][$request_method];

    // Check for middleware
    if (is_null($callback)) {
      $callback = $middleware_callback;
      $full_route['middleware'] = [];
    }
    else {
      $full_route['middleware'] = $middleware_callback;
    } 

    $full_route['callback'] = $callback;
    $full_route['params'] = array_map(function($param) { return trim($param, ':'); }, $params);
    $full_route['path'] = '/'.$this->route_name.'/'.$method_name;
    $full_route['request_method'] = $request_method;
  }

  /**
   * @todo change (middleware) redirect() to end().
   */
  private static function activate($route, $method, $req_method='GET', $params=[]) {
    $full_route = &self::$routes_data[$route][$method][$req_method];

    // Middleware
    foreach($full_route['middleware'] as $middleware) {
      if (!$middleware())
        (new Response)->end("Middleware blocks you from this route.");
    }

    $params = self::create_param_array($params, $full_route['params']);
    $full_route['callback'](new Request($params), new Response);
  }

  // Route suggestion
  private static function suggest_and_activate($route, $method, $req_method, $params) {
    $suggested_route = self::suggest_route($route, $req_method); 

    if (!is_null($suggested_route)) {
      $suggested_method = self::suggest_method($suggested_route, $method, $req_method);

      if (!is_null($suggested_method)) {
        // TODO: Activate disabled
        echo "Did u mean '/$suggested_route/$suggested_method' ($req_method)<br>";
        // self::activate($suggested_route, $suggested_method, $req_method, $params);
      }
      else { die("There is no valid route method for '/$suggested_route/$method' ($req_method)"); }

    }
    else { die("There is no valid route for '/$route' ($req_method)"); }
  }

  private static function suggest_route($route, $request_method) {
    if (isset(self::$routes_data[$route])) {
      return $route;
    }

    $possible_routes = self::get_all_possible_routes($request_method);
    if (empty($possible_routes)) {
      return null;
    }

    return self::get_best_possible_route($route, $possible_routes);
  }
  private static function get_all_possible_routes($request_method) {
    $possible_routes = [];

    foreach(array_keys(self::$routes_data) as $current_route) {
      foreach(array_keys(self::$routes_data[$current_route]) as $current_method) {
        if (self::does_route_exist($current_route, $current_method, $request_method))
          $possible_routes[] = $current_route;
      }
    }

    return array_unique($possible_routes);
  }
  private static function get_best_possible_route($route, $possible_routes) {
    $best_route = null;
    $best_score = 0;

    foreach($possible_routes as $current_route) {
      $score = calculate_word_score($route, $current_route);

      if ($score > $best_score) {
        $best_score = $score;
        $best_route = $current_route;
      }
    }

    return $best_route;
  }

  private static function suggest_method($route, $method, $request_method) {
    if (self::does_route_exist($route, $method, $request_method)) {
      return $method;
    }

    $possible_methods = self::get_all_possible_methods($route, $request_method);
    if (empty($possible_methods)) {
      return null;
    }

    return self::get_best_possible_method($method, $possible_methods);
  }
  private static function get_all_possible_methods($route, $request_method) {
    $possible_methods = [];

    foreach(array_keys(self::$routes_data[$route]) as $current_method) {
      if (self::does_route_exist($route, $current_method, $request_method))
        $possible_methods[] = $current_method;
    }

    return array_unique($possible_methods);
  }
  private static function get_best_possible_method($method, $possible_methods) {
    return self::get_best_possible_route($method, $possible_methods);
  }


  // Route availability checking
  private static function does_route_exist($route, $method, $request_method) {
    return isset(self::$routes_data[$route][$method][$request_method]);
  }


  // Helper functions pt 2
  private static function create_param_array($param_values, $param_keys) {
    $param_values_len = count($param_values);
    $param_keys_len = count($param_keys);

    $new_param_array = [];
    for($i = 0; $i < $param_values_len && $i < $param_keys_len; ++$i) {
      $new_param_array[$param_keys[$i]] = $param_values[$i];
    }

    return $new_param_array;
  }

  private static function parse_url($url=null) {
    $url = $url ?: $_GET['url'];
    
    $url = trim($url, '/');
    $url = filter_var($url, FILTER_SANITIZE_URL);
    $url = explode('/', $url);
    $url = array_filter($url);

    return $url;
  }  

  private static function get_request_method() {
    $request_method = $_SERVER['REQUEST_METHOD'];

    if ($request_method !== 'POST')
      return $request_method;
      
    return $_POST['REQUEST_METHOD'] ?? 'POST';
  }
};