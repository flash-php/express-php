<?php

use FlashPHP\core\http\Router;

// Load FlashPHP modules
require_once '_modules/autoload.php';

// Load user config
require_once 'app/config/config.php';


// Set up routes
$auth = new Router('/auth');
$home = new Router('/home');
$files = new Router('/files');

// Start router
Router::start();
// Router::print_all();