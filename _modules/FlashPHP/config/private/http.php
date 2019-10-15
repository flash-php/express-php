<?php

use FlashPHP\helpers\OldConfig;
use FlashPHP\core\engines\MTemplatingEngine;

// Router advanced
OldConfig::default('PATH_COMPONENTS', './app/components');
OldConfig::default('PATH_MIDDLEWARE', './app/middleware');
OldConfig::default('PATH_MODELS', './app/models');
OldConfig::default('PATH_ROUTES', './app/routes');
OldConfig::default('PATH_TEMPLATES', './app/templates');
OldConfig::default('PATH_VIEWS', './app/views');

OldConfig::default('DEFAULT_ROUTER_METHOD', 'index');
OldConfig::default('DEFAULT_ROUTER_ROUTE', 'home');

OldConfig::default_obj('TEMPLATE_ENGINE', new MTemplatingEngine());
