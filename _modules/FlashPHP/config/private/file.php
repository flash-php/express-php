<?php

use FlashPHP\helpers\OldConfig;

// All files
OldConfig::default('FILE_SIZE_MB', 2);
OldConfig::default('FILE_PATH', HOME.'private/images/upload/');
OldConfig::default('FILE_NAME', 'file');
OldConfig::default('FILE_TYPES', [
  // Image Types
  'image/gif',
  'image/jpeg',
  'image/pjpeg',
  'image/png',

  // Video Types

  // Document types

  // ...
]);



// Images
OldConfig::default('IMAGE_TYPES', [
  'image/gif',
  'image/jpeg',
  'image/pjpeg',
  'image/png',
]);