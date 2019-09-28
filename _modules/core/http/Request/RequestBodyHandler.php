<?php


class RequestBodyHandler extends AdvancedNullObject {
  public function __construct() {
    parent::__construct($this->get_body_array());
  }


  /**
   * @todo Create validation function
   */
  public function validate() {
    echo 'Validating...';
  }


  /**
   * Get_body_array
   * @return array Geeft de body array terug.
   */
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
}