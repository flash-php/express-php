<?php 

// Routes
global $home;
global $auth;


$home->get('/index', function($req, $res) {
  // $res->send_r($_GET);

  $res->view('request/putform');
});

$home->post('/index', function($req, $res) {
  $res->send('POST');
});

$home->put('/index', function($req, $res) {
  $res->send('PUT');

});

$home->delete('/index/:id', function($req, $res) {
  $res->send_r($_POST);
  // $res->send_r($req->params);
});