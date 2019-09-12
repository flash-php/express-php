<?php

/**
 * Router Class
 * @author Ingo Andelhofs
 * 
 * A class that lets u create new RESTful routes and also routes these so u can access them.
 * 
 * @uses RouterReqArg class
 * @uses RouterResArg class
 */
class Router {
  // Variables
  private $route_name;
  private static $routes_data = [];

  // Config variables
  private static $conf_set = false;
  private static $conf_mode = 'NullObject'; // TODO: $req->body->uname, $req->body['uname'].
  private static $conf_path;
  private static $conf_route;
  private static $conf_method;
  private static $conf_template_engine;

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
    if (!self::$conf_set) self::config();
    self::include_config_paths();

    $_GET['url'] ?? die("U forgot to add '?url=' at the end of your url...");

    $url = self::parse_url();
    $req_method = self::get_request_method();
    
    $route = $url[0] ?? self::$conf_route;
    $method = $url[1] ?? self::$conf_method;
    $params = isset($url[2]) ? array_slice($url, 2) : [];

    if (self::does_route_exist($route, $method, $req_method)) {
      self::activate($route, $method, $req_method, $params); 
    }
    else {
      self::suggest_and_activate($route, $method, $req_method, $params);
    }
  }

  public static function config($config_assoc_array = []) {
    self::$conf_set = true;

    self::$conf_path['components'] = $config_assoc_array['path']['components'] ?? './components';
    self::$conf_path['templates'] = $config_assoc_array['path']['templates'] ?? './templates';
    self::$conf_path['routes'] = $config_assoc_array['path']['routes'] ?? './routes';
    self::$conf_path['models'] = $config_assoc_array['path']['models'] ?? './models';
    self::$conf_path['views'] = $config_assoc_array['path']['views'] ?? './views';

    self::$conf_route = $config_assoc_array['default_route'] ?? "home";
    self::$conf_method = $config_assoc_array['default_method'] ?? "index";

    self::$conf_template_engine = $config_assoc_array['template_engine'] ?? null;
  }

  private static function include_config_paths() {
    include_all_r(self::$conf_path['routes']);
    include_all_r(self::$conf_path['models']);
  }

  // Developing functions
  public static function print_all() {
    echo '<pre>';
    print_r( self::$routes_data );
    echo '</pre>';
  }

  // Template engine
  public static function set_template_engine($name) {
    // TODO: check if the engine exists
    self::$conf_template_engine = $name;
  }
  public static function is_template_engine_set() {
    return !empty(self::$conf_template_engine);
  }
  public static function compile_render_template($view_path, $view_data) {
    if (class_exists(self::$conf_template_engine) && method_exists(self::$conf_template_engine, 'compile_render')) {
      call_user_func(self::$conf_template_engine.'::compile_render', $view_path, $view_data);
    }
    else {
      echo "Please check your Templating engine. The class or compile method was not found.";
    }
  }
  
  // Helper functions
  private function req($method, $request_method, $middleware_callback, $callback=null) {
    // Split up method to get parameters (parameters -> routes_data)
    $method = self::parse_url($method);
    $method_name = $method[0] ?? self::$conf_method;
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
      if (!eval("return $middleware;")) // call_user_func($middleware)
        (new RouterResArg)->redirect('/'.self::$conf_route.'/'.self::$conf_method);
    }

    $params = self::create_param_array($params, $full_route['params']);
    $full_route['callback'](new RouterReqArg($params), new RouterResArg);
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


/**
 * RouterRequestObject Class
 * @author Ingo Andelhofs
 * 
 * A class that handles all the Request functionalities.
 * 
 * @uses DataBase class
 * @uses GetNullObj / AdvanvcedNullObject
 */
class RouterReqArg {
  public $body;
  public $params;
  
  public $db;

  public $session;
  public $cookie;

  public function __construct($param_array) {
    $this->body = new AdvancedNullObject($this->get_body_array());
    $this->params = new AdvancedNullObject($param_array);

    $this->db = new DataBase();

    $this->session = new AdvancedNullObject($_SESSION);
    
  }

  private function &get_body_array() {
    switch ($_SERVER['REQUEST_METHOD']) {
      case 'GET':
        return $_GET;
        break;
      case 'POST':
        unset($_POST['REQUEST_METHOD']);
        return $_POST;
        break;
      case 'PUT':
        parse_str(file_get_contents('php://input'), $_PUT);
        return $_PUT;
        break;
      default:
        return [];
        break;
    }
  }
};


/**
 * RouterResponseObject Class
 * @author Ingo Andelhofs
 * 
 * A class that handles all the Response functionalities.
 * 
 * @uses Router class (template_engine functions)
 */
class RouterResArg {
  // Writing to the screen
  public function send($data='') {
    echo $data;
  }
  
  public function send_r($data='') {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
  }

  public function json($data=[]) {
    echo '<pre>';
    echo json_encode($data);
    echo '</pre>';
  }

  public function view($path='home/index', $data=[]) {
    $full_path = "./views/$path.php";

    if (Router::is_template_engine_set()) {
      Router::compile_render_template($path, $data);
    }
    else if (file_exists($full_path)) {
      include_once($full_path);
    }
    else {
      echo "Please check your view folder to make sure u created a view called '$path'.";
    }
  }

  public function render($path='home/index', $data=[]) {
    $this->view($path, $data);
  }

  public function js_log($data='') {
    echo "<script>";
    echo "console.log('$data');";
    echo "</script>";
  }


  // Ending the program
  public function end($data='') {
    die($data);
  }
  public function middleware($middleware_array, $redirect_route='/home/index') {
    array_product($middleware_array) ?: $this->redirect($redirect_route);
  }


  // Redirecting
  public function redirect($to='/home/index') {
    header("Location: $to");
    $this->end("Redirecting to: $to...");
  }

  public function redirect_back() {
    $this->redirect($_SERVER['HTTP_REFERER']); // $this->redirect('javascript://history.go(-1)');
  }


  // File handeling 
  public function download() {
    // Code here...
  }
};