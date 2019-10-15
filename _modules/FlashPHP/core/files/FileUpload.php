<?php

namespace FlashPHP\core\files;

/**
 * FileUpload Class
 * @author Ingo Andelhofs
 */

class FileUpload {
  // Member variables
  private $nofile = false;
  private $file_moved = false;

  public $full_name;
  public $name;
  public $extension;
  public $tmp_name;
  public $type;
  public $real_type;
  public $error;
  public $size;

  private $max_size = 0; // In bytes
  private $dest_filename = null;
  private $dest_folder = null;
  private $full_dest_path = null;

  private $valid_types = FILE_TYPES;

  // Constructor & debugInfo
  public function __construct($upload_name=null) {
    if (is_null($upload_name)) {
      $this->nofile = true;
    }
    else {
      // Multiple files
      if (is_array($upload_name)) {
        // TODO: Add array support
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

        $this->max_size =  FILE_SIZE_MB * pow(2, 20);
      }
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
  private function set_max_size($megabytes, $bytes=0) {
    $this->max_size = $megabytes * pow(2, 20) + $bytes;
  }
  private function set_dest_filename($name = null) {
    $name = is_null($name) ? uniqid() : $name;
    $this->dest_filename = $name . '.' . $this->extension;
  }
  private function set_dest_folder($path) {
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
  private function check_errors() {
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
  private function check_size() {
    if ($this->size > $this->max_size) {
      throw new RuntimeException('Exceeded file-size limit.');
    }
  }
  private function check_types() {
    if ($this->type !== $this->real_type) {
      throw new RuntimeException('Mimetypes do not match.');
    }

    if (!in_array($this->real_type, $this->valid_types)) {
      throw new RuntimeException('The file-type is not valid.');
    }
  }
  private function check_all() {
    $this->check_errors();
    $this->check_size();
    $this->check_types();
  }

  // Storing files
  private function move_file() {
    if ($this->file_moved)
      throw new RuntimeException('File already moved/stored.');

    if (!file_exists($this->dest_folder))
      throw new RuntimeException('Cant move because destination folder doesn\'t exist.');

    $move_result = move_uploaded_file($this->tmp_name, $this->gen_full_dest_path());

    if ($move_result)
      $this->file_moved = true;
    else
      throw new RuntimeException('Failed to store/move uploaded file.');
  }


  // User Interface functions
  public function storeAs($destination_path, $filename) {
    $this->check_all();
    $this->set_dest_filename($filename);
    $this->set_dest_folder($destination_path);
    $this->move_file();
    return $this->dest_filename;
  }
  public function store($destination_path) {
    return $this->storeAs($destination_path, null);
  }
};
