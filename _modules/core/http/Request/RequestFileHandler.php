<?php


class RequestFileHandler {
  private $uploaded_files;

  public function __construct() {
    // Get all file info
    $this->getUploadedFiles();
  }
  public function __get($name) {
    return $this->uploaded_files[$name] ?? new FileUpload();
  }

  private function getUploadedFiles() {
    foreach(array_keys($_FILES) as $file) {
      // TODO: Check for multi file uploads under same name attribute name='files[]'. https://www.php.net/manual/en/reserved.variables.files.php
      $this->uploaded_files[$file] = new FileUpload($file);
    }
    return $this->uploaded_files;
  }
  public function hasFiles() {
    return !empty($_FILES);
  }
}