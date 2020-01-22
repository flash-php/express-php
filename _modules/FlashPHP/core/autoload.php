<?php

// Core: exceptions
include 'exceptions/FlashPhpException.php';

// Core: http files
include 'middleware/Middleware.php';

// Core: http files
include 'http/Router.php';
include 'http/Route.php';

include 'http/Response/Response.php';

include 'http/Request/Request.php';
include 'http/Request/RequestBodyHandler.php';
include 'http/Request/RequestFileHandler.php';
include 'http/Request/RequestSessionHandler.php';
include 'http/Request/RequestCookieHandler.php';

// Core: database files
include 'database/Database.php';
include 'database/DatabaseSchema.php';

// Core: template engines
include 'engines/FlashTemplatingEngineException.php';

include 'engines/BaseTemplatingEngine.php';
include 'engines/NTemplatingEngine.php';
include 'engines/STemplatingEngine.php';
include 'engines/MTemplatingEngine.php';
include 'engines/Component.php';

// Core: file handlers
include 'files/FileUpload.php';

// Core: validation
include 'validation/Validator.php';
include 'validation/ValidationError.php';
include 'validation/FlashValidationException.php';

// Core: auth
include 'auth/Auth.php';

// Core: auth (passwords)
include 'auth/passwords/PasswordHandler.php';
include 'auth/passwords/LocalPassword.php';
include 'auth/passwords/FacebookPassword.php';
include 'auth/passwords/GooglePassword.php';