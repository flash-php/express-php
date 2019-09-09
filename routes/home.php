<?php global $home;

$home->get('/index', function($req, $res) {
  
  // $res->view('home/index', ['name' => 'Ingo']);
  $res->view('home/template', ['name' => 'Ingo']);

});

$home->get('/redirect_test', function($req, $res) {
  // $res->send('Redirect succesfull');
  $res->redirect_back();
  // $res->send_r($_SERVER);
});