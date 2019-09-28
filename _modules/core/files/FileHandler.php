<?php


class FileHandler {
  public static function returnImage(string $image_path) {
    // TODO: Check for security
    readfile($image_path);
  }

  private static function saveSingleFile($upload_name='file', $destination_folder='public/images/uploads/', $max_size_in_megabytes=1, $valid_types=IMAGE_TYPES, $valid_extensions=IMAGE_EXTS) {
    $file_info = $_FILES[$upload_name];
    $file_path_info = pathinfo($file_info['name']);
    $max_file_size = pow(2, 20) * $max_size_in_megabytes;
    $mime_info = (new finfo(FILEINFO_MIME_TYPE))->file($file_info['tmp_name']);


    // TODO: Separate validation logic.
    // Validation code
    if (!isset($_FILES[$upload_name]['error']) || is_array($_FILES[$upload_name]['error'])) {
      throw new RuntimeException('Invalid parameters.');
    }

    switch($_FILES[$upload_name]['error']) {
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

    if ($file_info['size'] > $max_file_size) {
      throw new RuntimeException('Exceeded file-size limit.');
    }

    if ($file_info['type'] !== $mime_info) {
      throw new RuntimeException('Mimetypes do not match.');
    }

    if (!in_array($file_info['type'], $valid_types)) {
      throw new RuntimeException('The file-type is not valid.');
    }
    if(!in_array($file_path_info['extension'], $valid_extensions)) {
      throw new RuntimeException('The file-extension is not valid.');
    }

    // Save file to given location
    $new_file_name = uniqid() . '.' . $file_path_info['extension'];
    $new_file_path = DIR . $destination_folder . $new_file_name;

    $move_result = move_uploaded_file($file_info['tmp_name'], $new_file_path);

    if (!$move_result) {
      throw new RuntimeException('Failed to store/move uploaded file.');
    }

  }

  private static function saveSingleFile2() {
    $file = new FileUploadInfo('file');
    $file->set_max_size(1);

    $file->check_all();

    $file->set_dest_filename();
    $file->set_dest_folder('public/images/uploads/');
    $file->store_file();
  }

  public static function saveSingleImage($upload_name='image', $destination_folder='public/images/uploads/') {
      try {
        self::saveSingleFile($upload_name, $destination_folder);
      }
      catch(RuntimeException $e) {
        echo $e->getMessage();
      }
    }
};