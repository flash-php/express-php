<?php

// Current dir
define('DIR', __DIR__.'/../../');

// Database 
define('DB_DRIVER', 'mysql');
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'express-php');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'Vxg[ycNV');

// Router
Router::config([
  'template_engine' => 'STE'
]);


// Router advanced
define('PATH_COMPONENTS', './components');
define('PATH_MODELS', './models');
define('PATH_VIEWS', './views');
define('PATH_TEMPLATES', './templates');
define('PATH_ROUTES', './routes');

define('DEFAULT_ROUTER_METHOD', 'index');
define('DEFAULT_ROUTER_ROUTE', 'home');
define('TEMPLATE_ENGINE', 'STE');



// Files
define('IMAGE_TYPES', ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/png']);
define('IMAGE_EXTS', ['gif', 'jpg', 'jpeg', 'pjpeg', 'png']);