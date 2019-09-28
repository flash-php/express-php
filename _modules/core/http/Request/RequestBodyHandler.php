<?php


class RequestBodyHandler extends AdvancedNullObject {
  // Constructor
  public function __construct() {
    parent::__construct($this->get_body_array());
  }
  private function &get_body_array() {
    switch ($_SERVER['REQUEST_METHOD']) {
      case 'GET':
        return $_GET;
        break;
      case 'POST':
        unset($_POST['REQUEST_METHOD']);
        return $_POST;
        break;
      case 'PUT':
        parse_str(file_get_contents('php://input'), $_PUT);
        return $_PUT;
        break;
      default:
        return [];
        break;
    }
  }

  // Functions
  public function validate() {
    // TODO: Create validation function.
    echo 'Validating...';
  }
}