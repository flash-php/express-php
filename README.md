# Flash - The PHP Framework
Created by Ingo Andelhofs  
Student at UHasselt (2019)

## Table of Contents
1. [Installation](#Installation)
2. [Router Usage](#Router-Usage)
3. [Database Usage](#Database-Usage)
3. [Simple Template Engine (STE) Usage](#Simple-Template-Engine-(STE)-Usage)
3. [JavaScript Helpers](#JavaScript-Helpers)
3. [In Development](#In-Development)


## Installation
### Simple dowload & require
To start using FlashPHP, u first need to add the `/_modules` folder to your project.  
Then u only need to require its `index.php` file.
```php
require_once './_modules/index.php';
```

### Composer install
Comming soon...

## Router Usage
The biggest part of FlashPHP is its Router. U can easily create RESTful routes and use their given `$request` and `$response` objects to handle basic route functionalities. 

### Initialization
> ./_modules/config/config.php
```php
// Here we set the template_engine to STE. 
// For the rest, we use the default configurations.
Router::config([
  'template_engine' => 'STE'
]);
```
> index.php
```php
// Create a base router. In this case the home route.
$home = new Router('/home');

// Include other route files (see later) or use the same file for small Routers.
// Here we create a simple GET route '/index' for the home route.
// You can access this route by going to '{hostname}/home/index'.
$home->get('/index', function ($request, $response) {
  // Code for route here...
});

// Start the router. It is IMPORTANT that you start the Router at the end.
Router::start();
```

### Default config
```php
$conf = [
  'path' => [
    'components' => './components',  
    'templates' => './templates',  
    'models' => './models',  
    'routes' => './components',  
    'views' => './views',  
  ],
  'default_route' => 'home',
  'default_method' => 'index',
  'template_engine' => null
];

Router::config($conf);
```

### Request methods
```php
$base_route = new Router('/base');

// GET (read) requests
$base_route->get('/index', function($req, $res) {/* Code here... */});

// POST (write/create) requests
$base_route->post('/index', function($req, $res) {/* Code here... */});

// PUT (update) requests
$base_route->put('/index', function($req, $res) {/* Code here... */});

// DELETE (delete) requests
$base_route->delete('/index', function($req, $res) {/* Code here... */});
```

### The $request object
```php
$route->get('/index/:param1/:param2', function($req, $res) {
  // Returns an object of $_REQUEST, $_POST, $_GET, ($_PUT, $_DELETE)
  $req->body;
  $req->body->name;

  // Returns an assoc array of $_REQUEST, $_POST, $_GET, ($_PUT, $_DELETE)
  $req->body_array;
  $req->body_array['name'];

  // Returns the parameter value given in the url or null if not given. 
  $req->params;
  $req->params->param2;

  // Returns a Database object. See later.
  $req->db;
  $req->db->create('User', ['name' => 'Jhon Doe', 'email' => 'jhond@example.com']);
})
```

### The $response object
```php
$route->get('/index', function($req, $res) {
  // Send text data to route.
  $res->send('Hello World!');

  // Send datastructures as text data to route (print_r).
  $res->send_r([3, 4, 5]);

  // Log a JavaScript message to the js console.
  $res->js_log('A console message.');

  // Convert a datastructure (array, ...) to json and send to route.
  $res->json(['name' => 'Ingo', 'age' => 19]); // -> {"name": "Ingo", "age": 19}

  // Render a view using the Template Engine. View files are in the view folder.
  $res->render('home/homepage');
  $res->view('home/homepage', ['title' => 'Welcome to the homepage.']);
  
  // End the script with a last message.
  $res->end();
  $res->end('End of the script');

  // Redirect to a given url. Or redirect back to the last visited url.
  $res->redirect('/home/index');
  $res->redirect_back();
  
  // Comming soon ...
  $res->download();
});
```

### Middleware & Auth
```php
class Auth {
  public static function is_user($param) {
    return function() use($param) {
      // Code here... 

      // Comming soon... (true, false)
      return Middleware::next();
      return Middleware::block();
    };
  }
};
```
```php
$auth->post('/admin_only', [Auth::is_user_logged_in(), Auth::is_user("admin")], function($req, $res) {
  // Code for admin only here... 
});
```

### File handeling
> in $route_callback($req, $res)
```php
// check for files
$req->hasFiles();

// returns uploaded files
$req->files;

// file info
$req->files->profile_picture->extension;
$req->files->upload->type;
$req->files->image->...;

// file storing
$filename = $req->files->profile_picture->store('public/images');
$filename = $req->files->profile_picture->storeAs('public/images', 'filename'); // filename.jpeg -> autocomplete extention

// Downloading
$res->download($path);
```

### Internal working
You can see this structure by using the `Router::print_all()` function.  
> ./routes/home.php
```php
// When u create a Router, it is added to Router::$routes_data. 
// All the information about the route is saved there.
$home = new Router('/home');
$home->get('/index/:id', function($req, $res) {
  // Code here...
});
```

> ./_modules/core/Router.php
```php
Router::$routes_data = [
    'home' => ['index' => ['get' => ['callback' => function($request, $response) {},
                                     'params' => [0 => 'id'],
                                     'path' => '/home/index', 
                                     'request_method' => 'GET', 
                                     'options' => [...]]
        ]
    ]
];
```

## Database Usage
### Initialization
It is simple to create a new DataBase, you just need to add some constants to your config.php file. These constants are the default arguments. 
> ./_modules/config/config.php
```php
define('DB_DRIVER', 'mysql');
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'db-name');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '********');
```
```php
$db = new DataBase();
$db2 = new DataBase($driver, $hostname, $port, $database_name, $username, $password);
```

### DataBaseSchemas
Create these database schemas for better prepared statements (comming soon...) and to get a visual representation of your table.
```php
new DataBaseSchema('User', [
  'id' => PDO::PARAM_INT,
  'firstname' => PDO::PARAM_STR,
  'lastname' => PDO::PARAM_STR,
  'email' => PDO::PARAM_STR,
  'password' => PDO::PARAM_STR
]);
```

### New 
You can create a new database function by using the `new()` function.
```php
DataBase::new('select_query', function($db, $table_name) {
  return $db->query("SELECT * FROM $table_name;");
});

$db = new DataBase();
$db->select_query('User'); // -> Array(User1, User2, ...);
```

### Query
A simple but save way to query.
```php
// Result contains an array with Users or in the case of an Insert the inserted Id property.
$result = $db->query("SELECT * FROM User WHERE id=:id AND firstname=:firstname;", [
  'id' => 3,
  'firstname' => 'Ingo'
]);
```

### Get
A simple select, read query.
```php
// WHERE id=5
$db->get('User', ['id' => 5]);

// WHERE id=3 OR id=4 OR id=5
$db->get('User', ['id' => [3, 4, 5]]);

// WHERE id=5 AND firstname='Ingo'
$db->get('User', ['id' => 5, 'firstname' => 'Ingo']);

// Comming soon...
$db->getUser(['id' => 5]);
$db->getUserById(5);
$db->getUserById([3, 4, 5]);
$db->getUserByIdAndFirstname(5, 'Ingo');
```

### Create
A simple insert, create query.
```php
$id = $db->create('User', ['firstname' => 'Ingo', 'Lastname' => 'A.']);

// Coming soon...
$db->createUser(['firstname' => 'Ingo', 'Lastname' => 'A.']);
```

### Delete
A simple delete query.
```php
$db->delete('User', ['id' => 3]);

// Coming soon...
$db->deleteUser(['id' => 3]);
$db->deleteUserById(3);
```

### Update
A simple update query.
```php
$db->update('User', ['id' => 3], ['firstname' => 'Ingo']);

// Coming soon...
$db->updateUser(['id' => 3], ['firstname' => 'Ingo']);
$db->updateUserById(3, ['firstname' => 'Ingo']);
```

### Exists
A simple check if an item exists.
```php
$db->exists('User', ['id' => 5, 'firstname' => 'Ingo']);

// Coming soon...
$db->existsUser(['id' => 5, 'firstname' => 'Ingo']);
$db->existsUserWithId(5);
$db->existsUserWithIdAndFirstname(5, 'Ingo');
```

### Duplicate
A simple duplicate query that returns the new id.
```php
$id = $db->duplicate('User', ['id' => 5]);

// Coming soon...
$db->duplicateUser(['id' => 5]);
$db->duplicateUserById(5);
```

## Simple Template Engine (STE) Usage
### A simple example
> index.php or ./_modules/config/config.php
```php
Router::set_template_engine('STE');
```

> ./routes/home.php
```php
$home->get('/index', function($req, $res) {
  $res->view('home/homepage', ['title' => 'Welcome to our page.']);
});
```
> ./views/home/homepage.php
```html
<h1><?= $title ?></h1>
```

> ./views/home/homepage2.php
```html
<!-- coming soon... -->
<h1>{{ $title }}</h1>

@if($title === 'Welcome'):
  // code here ...
@endif
```

### Templates
An easy way to include header, footer, scripts, ... into your main file.
> ./templates/TemplateName.php
```html
<html> 
  <head>
    @section scripts
  </head> 
  <body class="app">
    @section main
  </body>  
</html>
```
> ./views/home/template.php
```html
@extends TemplateName

@section scripts
<script src="./public/app.js"></script>

@section main
<h1>Welcom</h1>  
```

### Components
```php
Component::render('banner', ['title' => 'Welcome']);

// Or

Component::banner(['title' => 'Welcome']);
```

## JavaScript Helpers
### RESThelper.js
Support for REST forms.  
Supported methods: `GET`, `POST`, `PUT` and `DELETE`.
```html
<form action='/home/index' method='PUT'>
  <!-- Input fields -->
  <input type='submit' value='Click'>
</form>
```

Support for REST links.  
Supported attributes: `href`, `data-req`, `data-body` (except for DELETE) and `data-async` (coming soon...).
```html
<a href='/homs/index' data-req='PUT' data-body='{"name": "Ingo Andelhofs", "id": 5}'>Link to somewehere.</a>
<a href='/homs/index' data-req='DELETE' data-async='true'>Click and async.</a>
```

## In Development
### Sessions and cookies
```php
$home->get('/index', function($req, $res) {
    // Cookies
    $req->cookie->set();
    $req->cookie->read();
    $req->cookie->update();

    // Sessions
    $req->session->set();
    $req->session->read();
    $req->session->update();
});
```

### Validation
> in $route_callback($req, $res)
```php
$req->validate();
$req->body()->validate();
```

### Flash messages
> in $route_callback($req, $res)
```php
$res->flash($name, $message);
```
```php
Component::flash_message(); 
```