<?php

class Router {
  // Variables
  private $route_name;
  
  private static $route_array = [];
  private static $model_array;
  
  private static $default_route = 'home';
  private static $default_method = 'index';


  // Constructor
  public function __construct($route_name) {
    $this->route_name = ltrim($route_name, '/');
    self::$model_array[$this->route_name] = [];
  }

  
  // Functions
  public function set_models($model_array) {
    self::$model_array[$this->route_name] = $model_array;
  }

  // Req methodes
  public function get($method, $callback) { $this->req($method, 'GET', $callback); }
  public function post($method, $callback) { $this->req($method, 'POST', $callback); }
  public function put($method, $callback) { $this->req($method, 'PUT', $callback); }
  public function delete($method, $callback) { $this->req($method, 'DELETE', $callback); }
  
  private function req($method, $req_method, $callback) {
    self::$route_array[$this->route_name][ltrim($method, '/')][$req_method] = $callback;
  }


  // Testing
  public static function print_all() {
    echo '<pre>';
    print_r( self::$route_array );
    echo '</pre>';
  }



  public static function start() {
    // Check for ?url
    $_GET['url'] ?? die("U forgot to add '?url=' at the end of your url...");

    // Get URL params
    $url =          self::parse_url();
    $req_method =   self::get_request_method();
    $routes_data =  self::$route_array;
    

    $route =        $url[0] ?? self::$default_route;
    $method =       $url[1] ?? self::$default_method;
    $params =       isset($url[2]) ? array_slice($url, 2) : [];


    if (isset($routes_data[$route][$method][$req_method])) {
      self::activate($route, $method, $req_method, $params);
    }
    else {
      die("The default route doesn't exist.");
    }
    return;

    // // Check for default route
    // if ($url_len <= 0 || $original_url == '' || $original_url == '/') {
    //   (isset(self::$route_array[self::$default_route][self::$default_method])) ? // isset(self::$route_array[self::$default_route]) && 
    //     (self::activate(self::$default_route, self::$default_method, $req_method)) : 
    //     die("The default route doesn't exist.");

    //     return;
    // }

    // // Check for route
    // if ($url_len <= 1) {
    //   (isset(self::$route_array[$url[0]][self::$default_method])) ? // isset(self::$route_array[$url[0]]) && 
    //   (self::activate($url[0], self::$default_method, $req_method)) :
    //   die("There is no default route for '".$url[0]."'.");

    //   return;
    // }

    // if ($url >= 2) {
    //   (isset(self::$route_array[$url[0]][$url[1]])) ?  // isset(self::$route_array[$url[0]]) && 
    //     (self::activate($url[0], $url[1], $req_method, array_slice($url, 2))) : 
    //     die("There is no route for '".$url[0]."/".$url[1]."'.");
      
    //   return;
    // }
  }

  private static function activate($route, $method, $req_method='GET', $params=[]) {
    self::$route_array[$route][$method][$req_method](new RouterReqArg(self::$model_array[$route]), new RouterResArg, ...$params);
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
  public $model;

  public function __construct($model_array=[]) {
    $this->body = GetNullObj::create($_GET);
    $this->model = new Model($model_array);
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
};


class GetNullObj {
  public function __get($prop){
    return $this->$prop ?? null;
  }

  public static function create($values){
      if (is_array($values) !== true) return $values;

      $o = new static();
      foreach($values as $key => $value){
          $o->$key = static::create($value);
      }
      return $o;
  }
}