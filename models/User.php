<?php

$UserModel = new Model('User', 'Users');

$UserModel->schema([
  'id' => PDO::PARAM_INT,
  'contact_id' => PDO::PARAM_INT,
  'firstname' => PDO::PARAM_STR,
  'lastname' => PDO::PARAM_STR,
  'email' => PDO::PARAM_STR,
  'password' => PDO::PARAM_STR,
  'created_at' => PDO::PARAM_STR
]);

$UserModel->new('special_query', function($db, $to, $from) {
  $db->query("SELECT * FROM ...");

  $db->select();
  $db->create();
  $db->update();
  $db->delete();
  
  var_dump($to); echo "<br>";
  var_dump($from); echo "<br>";
  
  // $req->models->special_query($to, $from);
});


