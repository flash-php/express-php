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



/**
 * ['hashed_password', 'password', ...]
 */
// $UserModel->new_query('doesPasswordCompare', function($data, $options) {});