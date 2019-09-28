<?php

/**
 * FileUploadInfo Class
 * @author Ingo Andelhofs
 *
 *
 */

class FileUploadInfo {
  // Member variables
  public $full_name;
  public $name;
  public $extension;
  public $tmp_name;
  public $type;
  public $real_type;
  public $error;
  public $size;

  public $max_size = 0;
  public $dest_filename = null;
  public $dest_folder = null;
  public $full_dest_path = null;

  private $valid_types = IMAGE_TYPES;

  // Constructor & debugInfo
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

      $this->full_name = $file_info['name'] ?? null;
      $this->type = $file_info['type'] ?? null;
      $this->tmp_name = $file_info['tmp_name'] ?? null;
      $this->error = $file_info['error'] ?? 0;
      $this->size = $file_info['size'] ?? 0;

      $path_info = pathinfo($this->full_name);
      $this->name = $path_info['filename'] ?? null;
      $this->extension = $path_info['extension'] ?? null;

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

  // Getters & setters
  public function set_max_size_in_megabytes($size=1) {
    $this->set_max_size_in_bytes(pow(2, 20) * $size);
  }
  public function set_max_size_in_bytes($size) {
    $this->size = $size;
  }
  public function set_max_size($megabytes=1, $bytes=0) {
    $this->max_size = $this->set_max_size_in_megabytes($megabytes) + $this->set_max_size_in_bytes($bytes);
  }

  public function set_dest_filename($name = null) {
    $name = is_null($name) ? uniqid() : $name;
    $this->dest_filename = $name . '.' . $this->extension;
  }
  public function set_dest_folder($path) {
    $this->dest_folder = $path;
  }
  private function gen_full_dest_path() {
    if (!is_null($this->dest_filename) && !is_null($this->dest_folder)) {
      $this->full_dest_path = DIR . $this->dest_folder . '/' . $this->dest_filename;
      return $this->full_dest_path;
    }
    throw new RuntimeException('Destination path not valid.');
  }

  // Checking
  public function check_errors() {
    if(is_null($this->error) || is_array($this->error)) {
      throw new RuntimeException('Wrong error arg.');
    }

    switch($this->error) {
      case UPLOAD_ERR_OK:
        break;
      case UPLOAD_ERR_NO_FILE:
        throw new RuntimeException('No file was sent.');
        break;
      case UPLOAD_ERR_INI_SIZE:
      case UPLOAD_ERR_FORM_SIZE:
        throw new RuntimeException('Exceeded file-size limit.');
        break;
      default:
        throw new RuntimeException('Unknown error.');
        break;
    }
  }
  public function check_size() {
    if ($this->size > $this->max_size) {
      throw new RuntimeException('Exceeded file-size limit.');
    }
  }
  public function check_types() {
    if ($this->type !== $this->real_type) {
      throw new RuntimeException('Mimetypes do not match.');
    }

    if (!in_array($this->real_type, $this->valid_types)) {
      throw new RuntimeException('The file-type is not valid.');
    }
  }
  public function check_all() {
    $this->check_errors();
    $this->check_size();
    $this->check_types();
  }

  // Storing files
  public function store_file() {
    $move_result = move_uploaded_file($this->tmp_name, $this->gen_full_dest_path());

    if (!$move_result) {
      throw new RuntimeException('Failed to store/move uploaded file.');
    }
  }
};
