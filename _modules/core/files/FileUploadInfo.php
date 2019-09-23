<?php

class FileUploadInfo {
  public $full_name;
  public $type;
  public $tmp_name;
  public $error;
  public $size;

  public $name;
  public $extension;

  public $real_type;


  public function __construct($upload_name='file') {
    // Multiple files
    if (is_array($upload_name)) {

    }
    // Single file
    else {
      if (!isset($_FILES[$upload_name])) {
        throw new RuntimeException('Uploaded file does not exist.');
      }

      $file_info = $_FILES[$upload_name];

      $this->full_name = $file_info['name'];
      $this->type = $file_info['type'];
      $this->tmp_name = $file_info['tmp_name'];
      $this->error = $file_info['error'];
      $this->size = $file_info['size'];

      $path_info = pathinfo($this->full_name);
      $this->name = $path_info['filename'];
      $this->extension = $path_info['extension'];

      $mime_info = (new finfo(FILEINFO_MIME_TYPE))->file($this->tmp_name);
      $this->real_type = $mime_info;
    }
  }

  public function __debugInfo() {
    return [
      'full_name' => $this->full_name,
      'name' => $this->name,
      'extension' => $this->extension,
      'tmp_name' => $this->tmp_name,

      'type' => $this->type,
      'real_type' => $this->real_type,

      'error' => $this->error,
      'size' => $this->size,
    ];
  }

  public function hasErrors() {
    return false;
  }


};