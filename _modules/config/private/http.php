<?php

// Router advanced
Config::default('PATH_COMPONENTS', './app/components');
Config::default('PATH_MIDDLEWARE', './app/middleware');
Config::default('PATH_MODELS', './app/models');
Config::default('PATH_ROUTES', './app/routes');
Config::default('PATH_TEMPLATES', './app/templates');
Config::default('PATH_VIEWS', './app/views');

Config::default('DEFAULT_ROUTER_METHOD', 'index');
Config::default('DEFAULT_ROUTER_ROUTE', 'home');

Config::default_obj('TEMPLATE_ENGINE', new MTemplatingEngine());
