<?php

// All files
Config::default('FILE_SIZE_MB', 2);
Config::default('FILE_PATH', HOME.'private/images/upload/');
Config::default('FILE_NAME', 'file');
Config::default('FILE_TYPES', [
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
Config::default('IMAGE_TYPES', [
  'image/gif',
  'image/jpeg',
  'image/pjpeg',
  'image/png',
]);