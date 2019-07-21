<?php

// Load modules and init app
include_once './_modules/index.php';


// Set up routes
$auth = new Router('/auth');
$home = new Router('/home');
include_all('./routes');

// Set ip models
include_all('./models');


Router::start();
// Router::print_all();


$db = new DataBase();
// TODO:
// $db->schema([
//   'id' => PDO::PARAM_INT,
//   'firstname' => PDO::PARAM_STR,
//   'lastname' => PDO::PARAM_STR,
//   'email' => PDO::PARAM_STR
// ]);

// $id = $db->query("INSERT INTO User (firstname, lastname, email) VALUES (:firstname, :lastname, :email);", [
//   'firstname' => 'Ingo',
//   'lastname' => 'Andelhofs',
//   'email' => 'ingom2000@gmail.cm'
// ]);

// $db->getBy('User', ['id' => 21, 'firstname' => 'Inga']);
// $db->select('User', ['firstname' => 'Ingo'], ["lastname LIKE %andel%", "firstname=:firstname"]);
// $db->getUserByIdAndFirstname(6, 'Ingo');
// $db->getUserBy(['id' => 3]);
// $db->getUserByIdAndFirstname([4, 5, 6], ['Ingo', 'Andel']);

// $db->create('User', [
//   'firstname' => 'Ingo',
//   'lastname' => 'Andelhofs', 
//   'email' => 'ingom2000@gmail.com'
// ]);

// $db->delete('User', ['id' => [4, 5]]);
// $db->update('User', ['id' => 3], ['firstname' => 'Ingo', 'lastname' => 'andelhofs']);
