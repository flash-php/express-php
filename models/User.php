<?php

// Database schema
new DatabaseSchema('User', [
  'id' => PDO::PARAM_INT,
  'contact_id' => PDO::PARAM_INT,
  'firstname' => PDO::PARAM_STR,
  'lastname' => PDO::PARAM_STR,
  'email' => PDO::PARAM_STR
]);


// Extended functions
Database::new('getAllUsers', function(Database $db) {
  return $db->query("SELECT * FROM User;");
});