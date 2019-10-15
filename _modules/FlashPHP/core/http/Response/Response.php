<?php

namespace FlashPHP\core\http\Response;
use FlashPHP\helpers\OldConfig;

/**
 * RouterResponseObject Class
 * @author Ingo Andelhofs
 *
 * A class that handles all the Response functionalities.
 *
 * @uses Router class (template_engine functions)
 */
class Response {
  // Writing to the screen
  public function send($data='') {
    print(htmlspecialchars($data));
  }

  public function error($data='') {
    $this->send_r($data);
  }

  public function send_r($data='') {
    echo "<pre style='font-family: inherit;'>";
    print_r($data);
    echo "</pre>";
  }

  public function json($data=[]) {
    echo '<pre>';
    echo json_encode($data);
    echo '</pre>';
  }

  public function view($name='home/index', $data=[]) {
    $engine = OldConfig::TEMPLATE_ENGINE();
    $engine->compile_render($name, $data);
    $this->end();
  }

  public function render($path='home/index', $data=[]) {
    $this->view($path, $data);
  }

  public function js_log($data='') {
    echo "<script>";
    echo "console.log('$data');";
    echo "</script>";
  }


  // Ending the program
  public function end($data='') {
    die($data);
  }


  // Redirecting
  public function redirect($to='/home/index') {
    header("Location: $to");
    $this->end("Redirecting to: $to...");
  }

  public function redirect_back() {
    $this->redirect($_SERVER['HTTP_REFERER']); // $this->redirect('javascript://history.go(-1)');
  }


  // File handeling
  public function readfile(string $filename) {
    // TODO: Check for security
    if (file_exists($filename))
      readfile($filename);
    exit;
  }

  public function download(string $filename) {
    if (file_exists($filename)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.basename($filename).'"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($filename));
      readfile($filename);
    }
    exit;
  }
};