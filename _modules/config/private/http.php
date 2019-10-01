<?php

// Router advanced
Config::default('PATH_COMPONENTS', './components');
Config::default('PATH_MODELS', './models');
Config::default('PATH_VIEWS', './views');
Config::default('PATH_TEMPLATES', './templates');
Config::default('PATH_ROUTES', './routes');
Config::default('PATH_MIDDLEWARE', './middleware');

Config::default('DEFAULT_ROUTER_METHOD', 'index');
Config::default('DEFAULT_ROUTER_ROUTE', 'home');

Config::default_obj('TEMPLATE_ENGINE', new MTemplatingEngine());
