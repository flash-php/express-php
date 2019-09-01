# FlashWork - The PHP Framework
Created by Ingo Andelhofs  
Student at UHasselt (2019)

## Installation
Comming soon...

## Examples
#### Hello World
```php
include_once './_modules/index.php';

$home = new Router('/home');

$home->get('/index', function($req, $res) {
    $res->send('<h1>Hello World.<h1>');
});

Router::start();
```

#### Request Methods
```php
$methods = new Router('/methods');

$methods->get('/index', function($req, $res) {});
$methods->post('/index', function($req, $res) {});
$methods->put('/index', function($req, $res) {});
$methods->delete('/index', function($req, $res) {});
```

#### $REQuest argument
```php
$home->get('/index/:amount/:offset', function($req, $res) {
    // $_GET['username'];
    $req->body->username;
    $req->body_array['username'];
    
    // Url params
    $req->params->amount;
    $req->params->offset;

    // Cookies
    $req->cookie->set();
    $req->cookie->read();
    $req->cookie->update();

    // Sessions
    $req->session->set();
    $req->session->read();
    $req->session->update();
    
    // Models
    $req->models->getUserById();
});
```

#### RESponse argument
```php
$home->get('/index', function($req, $res) {
    $res->send();
    $res->json();
    $res->render();
    $res->view();
    
    $res->end();

    $res->redirect();
    $res->redirect_back();
    
    $res->download();
});
```

#### Models
```php
$UserModel = new Model('User', 'Users');
$UserModel->schema([
    'id' => PDO::PARAM_INT,
    'firstname' => PDO::PARAM_STR,
    'lastname' => PDO::PARAM_STR,
    'email' => PDO::PARAM_STR
]);

```

#### Models SQL
```php
$db->query("SELECT * FROM User WHERE id=:id AND firstname=:firstname;", [
  'id' => 3,
  'firstname' => 'Ingo'
]);
```

```php
$db->select('User', ['firstname' => 'Ingo'], ["lastname LIKE %andel%", "firstname=:firstname"]);
$db->getBy('User', ['id' => 3, 'firstname' => 'Ingo']);
$db->getUserBy([
  'id' => 3, 
  'firstname' => 'Ingo'
]);
$db->getUserById(3);
$db->getUserByIdAndFirstname(3, 'Ingo');
```

```php
$db->create('User', [
  'firstname' => 'Ingo',
  'lastname' => 'Andelhofs', 
  'email' => 'ingom2000@gmail.com'
]);
$db->createUser([
  'firstname' => 'Ingo',
  'lastname' => 'Andelhofs', 
  'email' => 'ingom2000@gmail.com'
]);
$user_id = $db->createUserReturnId([
  'firstname' => 'Ingo',
  'lastname' => 'Andelhofs', 
  'email' => 'ingom2000@gmail.com'
]);
```

```php
$db->delete('User', ['id' => 3]);
$db->deleteUser(['id' => 3]);
$db->deleteUserById(3);
```

```php
$db->update('User', ['id' => 3], ['firstname' => 'Ingo']);
$db->updateUser(['id' => 3], ['firstname' => 'Ingo']);
$db->updateUserById(3, ['firstname' => 'Ingo']);
```

```php
$db->duplicateUserById(3);
$db->doesUserEmailExists();
```



## Internal Information
```php
// Router -> $routes_data
$home = new Router('/home');
$home->get('/index/{id}', function($req, $res) {

});

// Gives
Router::$routes_data = [
    'home' => ['index' => ['get' => ['callback' => function() {},
                                     'params' => [0 => 'id'],
                                     'path' => '/home/index/{id}', 
                                     'request_method' => 'GET', 
                                     'options' => '']
        ]
    ]
];


```