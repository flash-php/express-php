<?php 

// Routes
global $home;
global $auth;

class Auth2 {
  public static function is_user_logged_in($name='') {
    // Middleware
    return function() use($name) {
      if ($name === 'a') return true; // Middleware::NEXT()
      return false; // Middleware::BLOCK()
    };
  }
};


$home->get('/index', function(Request $req, Response $res) {

//  $req->db->create();

  // $res->send_r($_GET);
   $res->view('request/index');
});

$home->post('/index', function($req, $res) {
  $res->send('POST');
  $res->send_r($req->body);
});

$home->put('/index', [Auth2::is_user_logged_in('b')], function($req, $res) {
  $res->send('PUT');
  $res->send_r($req->body);
});

$home->delete('/index/:id', function($req, $res) {
  $res->send_r($_POST);
  // $res->send_r($req->params);
});