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