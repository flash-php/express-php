<?php global $auth;

// $auth->set_models(['User']);

$auth->get('/index/:id/:second_id', function($req, $res) {
  // $req->model->getUserById([5, 6, 8]);
  $req->model->special_query('a', 'b');
  // $req->model->getUserById();

  $res->send('Route working <br>');

  var_dump($req->params->id);

});

$auth->post('/login', function($req, $res) {
  // Code here...

});

$auth->put('/login', function() {
  // Code here...
});

$auth->delete('/login', function() {
  // Code here...
});


// $auth->helper('isUserLoggedIn', function($req, $res) {

// });