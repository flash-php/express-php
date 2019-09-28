<?php 

global $home;
global $auth;


$home->get('/index', [AuthMiddleware::is_user_logged_in('a')],  function(Request $req, Response $res) {

  $req->cookie->user = 'test';
  $res->send_r($_COOKIE);


//   $res->view('request/index');
});


$home->post('/index', function($req, $res) {


  $res->send('POST');
  $res->send_r($req->body);
});
$home->put('/index', function($req, $res) {
  $res->send('PUT');
  $res->send_r($req->body);
});
$home->delete('/index/:id', function($req, $res) {
  $res->send_r($_POST);
  // $res->send_r($req->params);
});