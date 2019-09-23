<?php

// Load modules and init app
require_once './_modules/index.php';

// Set up routes
$auth = new Router('/auth');
$home = new Router('/home');
$files = new Router('/files');

// Start router
Router::start();
// Router::print_all();