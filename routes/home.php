<?php 

// Routes
global $home;
global $auth;

class Auth {
  public static function is_user_logged_in($name) {
    if ($name === 'a') return true;
    return false;
  }
};


$home->get('/index', function($req, $res) {
  // $res->send_r($_GET);

  $res->view('request/putform');
});

$home->post('/index', function($req, $res) {
  $res->send('POST');
  $res->send_r($req->body);
});

$home->put('/index', ["Auth::is_user_logged_in('ingo')"], function($req, $res) {
  $res->send('PUT');
  $res->send_r($req->body);
});

$home->delete('/index/:id', function($req, $res) {
  $res->send_r($_POST);
  // $res->send_r($req->params);
});