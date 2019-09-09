<?php

new DataBaseSchema('User', [
  'id' => PDO::PARAM_INT,
  'contact_id' => PDO::PARAM_INT,
  'firstname' => PDO::PARAM_STR,
  'lastname' => PDO::PARAM_STR,
  'email' => PDO::PARAM_STR
]);