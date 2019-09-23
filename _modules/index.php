<?php

// Helper files
include './_modules/helpers/helpers.php';
include './_modules/helpers/GetNullObj.php';


// Core: http files
include './_modules/core/http/Router.php';
include './_modules/core/http/Route.php';
include './_modules/core/http/Response.php';
include './_modules/core/http/Request.php';
// Core: database files
include './_modules/core/database/Database.php';
include './_modules/core/database/DatabaseSchema.php';
// Core: template engines
include './_modules/core/engines/STE.php';
include './_modules/core/engines/Component.php';
// Core: file handlers
include './_modules/core/files/FileHandler.php';
include './_modules/core/files/FileUploadInfo.php';
// Core: validation
include './_modules/core/validation/Validator.php';
// Core: auth
include './_modules/core/auth/Auth.php';

// Config
include './_modules/config/config.php';