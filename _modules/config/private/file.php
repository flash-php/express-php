<?php

// All files
Config::default('FILE_SIZE', 2);
Config::default('FILE_PATH', DIR.'private/images/upload/');
Config::default('FILE_NAME', 'image');
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