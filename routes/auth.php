<?php global $auth;

$auth->set_models(['User']);

$auth->get('/index', function($req, $res) {
  // $req->model->getUserById([5, 6, 8]);

  $res->send('Route working');

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