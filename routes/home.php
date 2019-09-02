<?php global $home;

$home->get('/index', function($req, $res) {
  echo '/home/index';

  // $req->db->create('User', [
  //   'firstname' => 'Ingo',
  //   'lastname' => 'Andelhofs',
  //   'email' => 'ingom2000@gmail.com'
  // ]);
});