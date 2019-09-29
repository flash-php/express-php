<?php 

global $home;
global $auth;


$home->get('/index', [AuthMiddleware::is_user_logged_in('a')],  function(Request $req, Response $res) {


  $res->view('home/template', ['name' => 'Ingo']);
});