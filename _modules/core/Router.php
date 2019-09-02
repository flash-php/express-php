<?php

class Router {
  // Variables
  private $route_name;
  
  private static $routes_data = []; 
  private static $default_route = 'home';
  private static $default_method = 'index';


  // Constructor
  public function __construct($route_name) {
    $this->route_name = trim($route_name, '/');
  }

  // Functions
  public function get($method, $callback) { $this->req($method, 'GET', $callback); }
  public function post($method, $callback) { $this->req($method, 'POST', $callback); }
  public function put($method, $callback) { $this->req($method, 'PUT', $callback); }
  public function delete($method, $callback) { $this->req($method, 'DELETE', $callback); }

  public static function start() {
    $_GET['url'] ?? die("U forgot to add '?url=' at the end of your url...");

    $url = self::parse_url();
    $req_method = self::get_request_method();
    
    $route = $url[0] ?? self::$default_route;
    $method = $url[1] ?? self::$default_method;
    $params = isset($url[2]) ? array_slice($url, 2) : [];

    isset(self::$routes_data[$route][$method][$req_method]) ? 
      (self::activate($route, $method, $req_method, $params)) : 
      die("There is no valid route for /$route/$method ($req_method).");
  }

  public static function setDefault() {}


  // Developing functions
  public static function print_all() {
    echo '<pre>';
    print_r( self::$routes_data );
    echo '</pre>';
  }
  
  // Helper functions
  private function req($method, $request_method, $callback) {
    // Split up method to get parameters (parameters -> routes_data)
    $method = self::parse_url($method);
    $method_name = $method[0] ?? self::$default_method;
    $params = isset($method[1]) ? array_slice($method, 1) : [];

    // Create routes_data
    $full_route = &self::$routes_data[$this->route_name][$method_name][$request_method];
    $full_route['callback'] = $callback;
    $full_route['params'] = array_map(function($param) { return trim($param, ':'); }, $params);
    $full_route['path'] = '/'.$this->route_name.'/'.$method_name;
    $full_route['request_method'] = $request_method;
    $full_route['options'] = [];
  }

  private static function activate($route, $method, $req_method='GET', $params=[]) {
    $full_route = &self::$routes_data[$route][$method][$req_method];

    $params = self::createParamArray($params, $full_route['params']);
    $full_route['callback'](new RouterReqArg($params), new RouterResArg);
  }

  private static function createParamArray($param_values, $param_keys) {
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
    return strtoupper($_SERVER['REQUEST_METHOD']);
  }
};


class RouterReqArg {
  public $body;
  public $params;
  public $db;

  public function __construct($param_array) {
    $this->body = GetNullObj::create($_GET);
    $this->params = GetNullObj::create($param_array);
    $this->db = new DataBase();
  }
};

class RouterResArg {
  public function view($path) {
    echo "View path: $path";
  }

  public function send($data) {
    echo $data;
  }
  
  public function send_r($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
  }

  public function end($data) {
    die($data);
  }

  public function js_log($data) {
    echo "<script>";
    echo "console.log('$data');";
    echo "</script>";
  }
};