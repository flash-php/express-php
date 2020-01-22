<?php

use FlashPHP\core\http\Request\Request;
use FlashPHP\core\http\Response\Response;

global $home;
global $auth;


$home->get('/index', [AuthMiddleware::is_user_logged_in('a')],  function(Request $req, Response $res) {
  $res->view('home/template', ['name' => 'Ingo']);
});


/**
 * Validation routes
 */
$home->get('/validation', function(Request $req, Response $res) {
  $res->view('home/validation');
});

$home->post('/validation', function(Request $req, Response $res) {

  $req->body->validate([
    'firstname' => '/['.REGEX_BASIC_LETTERS.' ]{1,128}/',
    'lastname' => ['regex' => '/[-\'a-zA-ZÀ-ÖØ-öø-ÿ ]{1,128}/']

  ])->foreach_error(function($err) {
    $err->display();

  })->end();




});