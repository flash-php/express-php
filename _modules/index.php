<?php

session_start();

// Helper files
include './_modules/helpers/helpers.php';
include './_modules/helpers/AdvancedNullObject.php';
include './_modules/helpers/Config.php';

// Core: exceptions
include './_modules/core/exceptions/FlashPhpException.php';

// Core: http files
include './_modules/core/middleware/Middleware.php';

// Core: http files
include './_modules/core/http/Router.php';
include './_modules/core/http/Route.php';
include './_modules/core/http/Response/Response.php';

include './_modules/core/http/Request/Request.php';
include './_modules/core/http/Request/RequestBodyHandler.php';
include './_modules/core/http/Request/RequestFileHandler.php';
include './_modules/core/http/Request/RequestSessionHandler.php';
include './_modules/core/http/Request/RequestCookieHandler.php';

// Core: database files
include './_modules/core/database/Database.php';
include './_modules/core/database/DatabaseSchema.php';

// Core: template engines
include './_modules/core/engines/TemplateEngineStrategy.php';
include './_modules/core/engines/TemplateEngineException.php';
include './_modules/core/engines/NoTemplatingEngine.php';
include './_modules/core/engines/SimpleTemplatingEngine.php';
include './_modules/core/engines/Component.php';

// Core: file handlers
include './_modules/core/files/FileUpload.php';

// Core: validation
include './_modules/core/validation/Validator.php';

// Core: auth
include './_modules/core/auth/Auth.php';



// Config
include './_modules/config/config.php';

include './_modules/config/private/config.php';
include './_modules/config/private/file.php';
include './_modules/config/private/database.php';
include './_modules/config/private/http.php';