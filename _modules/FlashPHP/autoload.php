<?php

// Interfaces
include 'interfaces/TemplatingEngineStrategy.php';
include 'interfaces/PasswordStrategy.php';

// Helper files
include 'helpers/helpers.php';
include 'helpers/AdvancedNullObject.php';
include 'deprecated/OldConfig.php';

require_once 'core/autoload.php';

// Config
include 'config/config.php';

include 'config/private/config.php';
include 'config/private/file.php';
include 'config/private/database.php';
include 'config/private/http.php';
include 'config/private/regex.php';